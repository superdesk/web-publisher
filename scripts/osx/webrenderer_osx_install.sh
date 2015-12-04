#!/bin/bash
########################################################################
# Utility script to install newscoop and all its dependencies on OSX
# using brew, curl, and php composer
#
SCRIPTPATH="`dirname \"$0\"`"
SCRIPTPATH="`( cd \"$SCRIPTPATH\" && pwd )`" 

########################################################################
# Process command line parameters
########################################################################
for i in "$@"
do
case $i in
    -p=*|--path=*)
    SOURCEPATH="${i#*=}"
    shift
    ;;
    -d|--dependencies)
    DEPENDENCIES=true
    shift
    ;;
    -i|--interactive)
    INTERACTIVE=true
    shift
    ;;
    *)
    # unknown option
    # TODO: print usage
    ;;
esac
done


###############################################################
# START dependency installs
#   For initial install only
###############################################################

#
# Check if Homebrew is installed
#
which -s brew
if [[ $? != 0 ]] ; then
    # Install Homebrew
    /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
fi

#
# Check for plsql
#
which -s postgres
if [[ $? != 0 ]] ; then
    brew install postgresql
fi

#
# Check if PHP is installed
#
which -s php
if [[ $? != 0 ]] ; then
    brew install php55
fi

#
# Check for PHP with intl
#
php -i | grep "PDO Driver for PostgreSQL => enabled"
if [[ $? != 0 ]] ; then
    echo "Installing php55-pdo-pgsql";
    brew install php55-pdo-pgsql
fi

#
# Check for composer
#
which -s composer 
if [[ $? != 0 ]] ; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

###############################################################
# END dependency installs
###############################################################

# apache config
# only execute this if parameter to prompt is used
# otherwise just use SCRIPTPATH to determine NSDIR
if [[ "$INTERACTIVE" = true ]]; then
    DEFAULT_NS_DIR=/Users/$(whoami)/Sites/Newscoop
    echo "Enter the Newscoop repository checkout dir, followed by [ENTER]: ($DEFAULT_NS_DIR)"
    read NSDIR 

    if [[ -z "$VAR" ]] ; then
        NSDIR=${DEFAULT_NS_DIR//\//\\/}
    else
        NSDIR=${NSDIR//\//\\/}
    fi
    echo "Using $NSDIR as web root for vhost"
else
    NSDIR=$(readlink -f “$SCRIPTPATH/../../”)
    NSDIR=${NSDIR//\//\\/}
fi

echo "Backing up existing /etc/apache2/httpd.conf"
sudo cp /etc/apache2/httpd.conf /etc/apache2/httpd.conf.bak
sudo cp $SCRIPTPATH/httpd.conf /etc/apache2/httpd.conf

echo "Backing up existing /etc/apache2/extra/httpd-vhosts.conf"
sudo cp /etc/apache2/extra/httpd-vhosts.conf /etc/apache2/extra/httpd-vhosts.conf.bak
(sed '/# START webrenderer.dev.conf/,/# END webrenderer.dev.conf/d' /etc/apache2/extra/httpd-vhosts.conf; cat webrenderer.dev.conf ) > $SCRIPTPATH/httpd-vhosts.conf.tpl
sudo sh -c "sed 's/\$WEBRENDERERDIR/$NSDIR/g' $SCRIPTPATH/httpd-vhosts.conf.tpl > /etc/apache2/extra/httpd-vhosts.conf"
sudo apachectl restart
rm -rf $SCRIPTPATH/httpd-vhosts.conf.tpl

# reset webrenderer config and database
CURRENTUSER=$(whoami)
sudo cp $SCRIPTPATH/../../app/config/parameters.yml $SCRIPTPATH/../../app/config/parameters.yml.bak
sudo sh -c "sed 's/\$DBUSER/$CURRENTUSER/g' $SCRIPTPATH/parameters.yml.tpl > $SCRIPTPATH/../../app/config/parameters.yml"
dropdb webrenderer
psql postgres -c "CREATE DATABASE webrenderer WITH ENCODING 'UTF8'"

# set permissions
#chmod 775 plugins install cache images public conf log

# composer install, and webrenderer install
cd $SCRIPTPATH/../../
composer self-update
composer install --prefer-dist

# install fixtures
php app/console doctrine:schema:update --force
php app/console doctrine:phpcr:repository:init
