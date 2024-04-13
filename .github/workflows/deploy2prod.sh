# This script requires the following
# environment variables to be set:
# SERVER_CERTIFICATE
# FTP_HOST
# FTP_PORT
# FTP_USER
# FTP_PASS

sudo apt update
sudo apt upgrade -y
sudo apt install -y ca-certificates openssl git-ftp composer pcregrep

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

############################################################
# RUN git-ftp TO SYNC REMOTE FILESYSTEM WITH THE LOCAL ONE #
############################################################

git config git-ftp.url "ftpes://$FTP_HOST:$FTP_PORT" \
&& git config git-ftp.user "$FTP_USER" \
&& git config git-ftp.password "$FTP_PASS"

if [ $? -ne 0 ]; then
    echo "GitFTP configuration went wrong"
    exit 1
fi

# For unknown reason, this file (deploy2prod.sh)
# got modified during the earlier steps. To avoid
# GitFTP's "dirty repo" error, we have to do this
# in prior to deploying:
git reset --hard

git ftp push
exit $?
