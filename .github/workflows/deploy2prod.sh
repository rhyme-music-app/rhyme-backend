# This script requires the following
# environment variables to be set:
# SERVER_CERTIFICATE
# FTP_HOST
# FTP_PORT
# FTP_USER
# FTP_PASS

sudo apt update
sudo apt upgrade -y
sudo apt install -y ca-certificates openssl lftp composer pcregrep

if [ $? -ne 0 ]; then
    echo "could not install some dependencies"
    exit 1
fi

############################
# INSTALL PHP DEPENDENCIES #
############################

echo "Using Composer: $(composer --version)"
composer install
composer dump-autoload

#############
# RUN TESTS #
#############

####################################################
# From now on is the DEPLOYMENT (CD) stuff, not CI #
####################################################

# https://stackoverflow.com/a/67793230/13680015
if [[ "$(git branch --show-current)" != "prod" ]]; then
    echo "script won't deploy to production since this is not the 'prod' branch ; everything is ok, exiting"
    exit 0
fi

############################
# UPDATE ROOT CERTIFICATES #
############################

sudo update-ca-certificates

if [ $? -ne 0 ]; then
    echo "could not update CA certificates"
    exit 1
fi

CA_FILE="/etc/ssl/certs/ca-certificates.crt"
if [ ! -f "$CA_FILE" ]; then
    echo "could not locate certificate chain"
    exit 1
fi

##########################
# ADD SERVER CERTIFICATE #
##########################

cat $CA_FILE | pcregrep -M -e "$SERVER_CERTIFICATE"
if [ $? -ne 0 ]; then
    sudo bash -c "echo '$SERVER_CERTIFICATE' >> $CA_FILE"
    if [ $? -ne 0 ]; then
        echo "could not add server certificate to the system's cert chain"
        exit 1
    fi
fi

##################
# CONFIGURE lftp #
##################

# Make lftp to use the CA certificates
# that are just installed and updated:

LFTP_CONFIG_FILE="/etc/lftp.conf"

if [ ! -f "$LFTP_CONFIG_FILE" ]; then
    $LFTP_CONFIG_FILE="~/.lftp/rc"
    if [ ! -f "$LFTP_CONFIG_FILE" ]; then
        $LFTP_CONFIG_FILE="~/.config/lftp/rc"
        if [ ! -f "$LFTP_CONFIG_FILE" ]; then
            echo "lftp config file not found"
            exit 1
        fi
    fi
fi

LFTP_SETUP_COMMAND="set ssl:ca-file \"$CA_FILE\""

cat $LFTP_CONFIG_FILE | pcregrep -M -e "$LFTP_SETUP_COMMAND"
if [ $? -ne 0 ]; then
    sudo bash -c "echo '$LFTP_SETUP_COMMAND' >> $LFTP_CONFIG_FILE"
    if [ $? -ne 0 ]; then
        echo "could not add lftp setup command to the lftp config file"
        exit 1
    fi
fi

#########################################################
# RUN lftp TO SYNC REMOTE FILESYSTEM WITH THE LOCAL ONE #
#########################################################

sudo lftp -u "$FTP_USER,$FTP_PASS" $FTP_HOST -p $FTP_PORT -e "ls ; mirror -P --continue --reverse --delete --verbose --exclude \\.env --exclude \\.ftpquota --exclude \\.git/ --exclude \\.github/ --exclude ^\\.htaccess\$ --exclude var ; exit"
exit 1
