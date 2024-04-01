# This script requires the following
# environment variables to be set:
# SERVER_CERTIFICATE
# FTP_HOST
# FTP_PORT
# FTP_USER
# FTP_PASS

apt update
apt upgrade -y
apt install -y ca-certificates openssl lftp composer

if [ $? -ne 0 ]; then
    echo "could not install some dependencies"
    exit $?
fi

############################
# UPDATE ROOT CERTIFICATES #
############################

update-ca-certificates

if [ $? -ne 0 ]; then
    echo "could not update CA certificates"
    exit $?
fi

CA_FILE="/etc/ssl/certs/ca-certificates.crt"
if [ ! -f "$CA_FILE" ]; then
    echo "could not locate certificate chain"
    exit 1
fi

##########################
# ADD SERVER CERTIFICATE #
##########################

cat $CA_FILE | grep "$SERVER_CERTIFICATE"
if [ $? -ne 0 ]; then
    echo $SERVER_CERTIFICATE >> $CA_FILE
    if [ $? -ne 0 ]; then
        echo "could not add server certificate to the system's cert chain"
        exit $?
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

cat $LFTP_CONFIG_FILE | grep "$LFTP_SETUP_COMMAND"
if [ $? -ne 0 ]; then
    echo $LFTP_SETUP_COMMAND >> $LFTP_CONFIG_FILE
    if [ $? -ne 0 ]; then
        echo "could not add lftp setup command to the lftp config file"
        exit $?
    fi
fi

############################
# INSTALL PHP DEPENDENCIES #
############################

composer install
composer dump-autoload

#########################################################
# RUN lftp TO SYNC REMOTE FILESYSTEM WITH THE LOCAL ONE #
#########################################################

lftp -u "$FTP_USER,$FTP_PASS" $FTP_HOST -p $FTP_PORT -e "mirror -P --continue --reverse --delete --verbose --exclude .env --exclude .ftpquota --exclude .git/ --exclude .github/ --exclude .htaccess ; exit"
exit $?
