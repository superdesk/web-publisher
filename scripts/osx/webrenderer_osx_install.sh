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
if [[ "$#" -eq "0" ]]; then
    echo -e "No arguments passed, defaulting to full intall without /etc/apache2/httpd.conf"
    DEPENDENCIES=true;
    VHOSTCONFIG=true;
    HOSTSCONFIG=true;
    DATABASEINSTALL=true;
    COMPOSERINSTALL=true;
    FIXTUREINSTALL=true;
fi

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
    --httpd-config)
    HTTPDCONFIG=true
    shift
    ;;
    --vhost-config)
    VHOSTCONFIG=true
    shift
    ;;
    --hosts-config)
    HOSTSCONFIG=true
    shift
    ;;
    --database-install)
    DATABASEINSTALL=true
    shift
    ;;
    --composer-install)
    COMPOSERINSTALL=true
    shift
    ;;
    --fixture-install)
    FIXTUREINSTALL=true
    shift
    ;;
    --full)
    FULL=true;
    DEPENDENCIES=true;
    HTTPDCONFIG=true;
    VHOSTCONFIG=true;
    HOSTSCONFIG=true;
    DATABASEINSTALL=true;
    COMPOSERINSTALL=true;
    FIXTUREINSTALL=true;
    shift
    ;;
    -h|--help)
    HELP=true
    shift
    ;;
    *)
    # unknown option
    HELP=true
    ;;
esac
done

#########################################
#  Help and Usage
#########################################
if [[ "$HELP" = true ]]; then
    echo -e "Usage:\n"
    echo -e "  $0 [OPTION]"
   
    echo -e "\nGeneral options:"
    echo -e "  -d, --dependencies \t install required dependencies (brew, postgres, php, and php extensions)" 
    echo -e "  -i, --interactive \t interactive install, prompt for all options" 
    echo -e "  -h, --help \t\t print this usage message"
    echo -e "\nInstall options:"
    echo -e "  --composer-install \t run composer install"
    echo -e "  --database-install \t creates new webrenderer psql database (WARNING: resets if it already exists)"
    echo -e "  --fixture-install \t install fixture data"
    echo -e "  --full \t\t install all dependencies, configs, and fixture data"
    echo -e "\nConfig options:"
    echo -e "  --httpd-config \t configure apache2 httpd daeman with defaults that will allow web-renderer to run"
    echo -e "  --vhost-config \t configure httpd vhost for webrenderer.dev"
    echo -e "  --hosts-config \t configure /etc/hosts for webrenderer.dev"
    echo -e "\n"
    exit 1
fi

#########################################
#  Depencies CONFIG
#########################################
if [[ "$INTERACTIVE" = true ]]; then
    echo "Install all dependencies (yY/nN)?:"
    read REPLY
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        DEPENDENCIES=true
    fi
fi
if [[ "$DEPENDENCIES" = true ]]; then

    # TODO: check for apache2?

    which -s brew
    if [[ $? != 0 ]] ; then
        echo "Installing brew";
        /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
    fi

    which -s postgres
    if [[ $? != 0 ]] ; then
        echo "Installing postgresql";
        brew install postgresql
    fi

    which -s php
    if [[ $? != 0 ]] ; then
        echo "Installing php56";
        brew install php5&
    fi

    php -i | grep "PDO Driver for PostgreSQL => enabled"
    if [[ $? != 0 ]] ; then
        echo "Installing php56-pdo-pgsql";
        brew install php56-pdo-pgsql
    fi

    which -s composer 
    if [[ $? != 0 ]] ; then
        echo "Installing composer";
        brew install composer
    fi
fi

#########################################
# Hosts CONFIG
#########################################
if [[ "$INTERACTIVE" = true ]]; then
    echo "Update /etc/hosts to include webrenderer.dev (yY/nN)?:"
    read REPLY
    if [[ $REPLY =~ ^[Yy]$ ]]; then
       HOSTSCONFIG=true
    fi
fi
if [[ "$HOSTSCONFIG" = true ]]; then
    # TODO: add webrenderer.dev to /etc/hosts
    cat /etc/hosts | grep "127.0.0.1 webrenderer.dev"
    if [[ $? != 0 ]] ; then
        sudo sh -c "echo '127.0.0.1 webrenderer.dev' >> /etc/hosts"
    fi
fi

#########################################
# HTTPD CONFIG
#########################################
if [[ "$INTERACTIVE" = true ]]; then
    echo "Install default httpd.config (yY/nN)?:"
    read REPLY
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        HTTPDCONFIG=true
    fi
fi
if [[ "$HTTPDCONFIG" = true ]]; then
    echo "Backing up existing /etc/apache2/httpd.conf"
    sudo cp /etc/apache2/httpd.conf /etc/apache2/httpd.conf.bak
    sudo cp $SCRIPTPATH/httpd.conf /etc/apache2/httpd.conf
fi


#########################################
# VHOST CONFIG
#########################################
if [[ "$INTERACTIVE" = true ]]; then
    echo "Install default httpd vhost config (yY/nN)?:"
    read REPLY
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        VHOSTCONFIG=true
    fi
fi
if [[ "$VHOSTCONFIG" = true ]]; then
    if [[ "$INTERACTIVE" = true ]]; then
        DEFAULT_NS_DIR=$(realpath $SCRIPTPATH/../../)
        echo "Enter the Newscoop repository checkout dir, followed by [ENTER]: ($DEFAULT_NS_DIR)"
        read NSDIR 

        if [[ -z "$VAR" ]] ; then
            NSDIR=${DEFAULT_NS_DIR//\//\\/}
        else
            NSDIR=${NSDIR//\//\\/}
        fi
        echo "Using $NSDIR as web root for vhost"
    else
        NSDIR=$(realpath $SCRIPTPATH/../../)
        NSDIR=${NSDIR//\//\\/}
    fi

    echo "Backing up existing /etc/apache2/extra/httpd-vhosts.conf"
    sudo cp /etc/apache2/extra/httpd-vhosts.conf /etc/apache2/extra/httpd-vhosts.conf.bak
    (sed '/# START webrenderer.dev.conf/,/# END webrenderer.dev.conf/d' /etc/apache2/extra/httpd-vhosts.conf; cat webrenderer.dev.conf ) > $SCRIPTPATH/httpd-vhosts.conf.tpl
    sudo sh -c "sed 's/\$WEBRENDERERDIR/$NSDIR/g' $SCRIPTPATH/httpd-vhosts.conf.tpl > /etc/apache2/extra/httpd-vhosts.conf"
    sudo apachectl restart
    rm -rf $SCRIPTPATH/httpd-vhosts.conf.tpl
fi

#########################################
# Database install
#########################################
if [[ "$INTERACTIVE" = true ]]; then
    echo "Install webrenderer database (yY/nN)?:"
    read REPLY
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        DATABASEINSTALL=true
    fi
fi
if [[ "$DATABASEINSTALL" = true ]]; then
    CURRENTUSER=$(whoami)
    sudo cp $SCRIPTPATH/../../app/config/parameters.yml $SCRIPTPATH/../../app/config/parameters.yml.bak
    sudo sh -c "sed 's/\$DBUSER/$CURRENTUSER/g' $SCRIPTPATH/parameters.yml.tpl > $SCRIPTPATH/../../app/config/parameters.yml"
    dropdb webrenderer
    psql postgres -c "CREATE DATABASE webrenderer WITH ENCODING 'UTF8'"
fi


#########################################
# Composer install
#########################################
if [[ "$INTERACTIVE" = true ]]; then
    echo "Run composer install (yY/nN)?:"
    read REPLY
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        COMPOSERINSTALL=true
    fi
fi
if [[ "$COMPOSERINSTALL" = true ]]; then
    cd $SCRIPTPATH/../../
    composer self-update
    composer install --prefer-dist
fi

#########################################
# Fixure data install
#########################################
if [[ "$INTERACTIVE" = true ]]; then
    echo "Install fixture data (yY/nN)?:"
    read REPLY
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        FIXTUREINSTALL=true
    fi
fi
if [[ "$FIXTUREINSTALL" = true ]]; then
    php app/console doctrine:schema:update --force
    php app/console doctrine:phpcr:repository:init
fi
