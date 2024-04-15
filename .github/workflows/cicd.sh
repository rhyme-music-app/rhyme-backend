sudo apt update
# sudo apt upgrade -y
sudo apt install -y composer

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

./vendor/bin/phpunit
if [ $? -ne 0 ]; then
    echo "tests failed ; please review your code, run tests locally, and fix all the issues detected"
    exit 1
fi
