How to Install and Configure Superdesk Publisher with Superdesk
===============================================================

This guide describes the functions of Superdesk Publisher and Superdesk, along with the required steps to run
both applications concurrently in a production environment on two **different** servers.
(However, both applications can also work on a single machine.)

How to Install Superdesk?
-------------------------

Prerequisites:
``````````````

- VPS or dedicated server: min 2GB RAM, 4GB Free space
- Ubuntu 16.04 server version installed

Superdesk can be installed using one command line script
which can be found here: https://github.com/takeit/superdesk-install/blob/master/install

Run command:

.. code-block:: bash

    sudo apt-get install curl -y
    && curl -s https://raw.githubusercontent.com/takeit/superdesk-install/master/install | sudo bash

.. note::

    If you see the following message:

    .. code:: bash

        The virtual environment was not created successfully because ensurepip is not
        available. On Debian/Ubuntu systems, you need to install the python3-venv
        package using the following command.
        apt-get install python3-venv
        You may need to use sudo with that command. After installing the python3-venv
        package, recreate your virtual environment.
        Failing command: [‘/opt/superdesk/env/bin/python3’, ‘-Im’, ‘ensurepip’, ‘ — upgrade’, ‘ — default-pip’]

    Run:

    .. code:: bash

        export LC_ALL="en_US.UTF-8"
        export LC_CTYPE="en_US.UTF-8"

    and then again execute the command:

    .. code:: bash

      sudo apt-get install curl -y
      && curl -s https://raw.githubusercontent.com/takeit/superdesk-install/master/install | sudo bash

The above command will install all the required dependencies needed by Superdesk.
Once this is done, the Superdesk will run on your server.
You will be able to access it via your browser: ``http://<ip_or_domain>``.

The default login credentials will be:

.. code-block:: bash

    Username: admin
    Password: admin


How to Install Superdesk Publisher?
-----------------------------------

Prerequisites:
``````````````

See `here`_ to read more about all requirements.

.. note::

    In this guide the Superdesk Publisher will be installed on another server.


Setting up the server/VPS
`````````````````````````

1. Install ElasticSearch
""""""""""""""""""""""""

ElasticSearch v5.6 will be used. Run the following command to install ES:

.. code-block:: bash

    curl -L -O https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-5.6.0.deb &&
    sudo dpkg -i elasticsearch-5.6.0.deb && sudo apt-get -y update &&
    sudo apt-get -y install --no-install-recommends openjdk-8-jre-headless &&
    sudo systemctl enable elasticsearch && sudo systemctl restart elasticsearch

The ElasticSearch should be running on port ``9200``. You can run the command:

.. code-block:: bash

    curl -s "http://localhost:9200"

to find out if all works fine.

2. Install PostgreSQL
"""""""""""""""""""""

Run command:

.. code-block:: bash

    sudo apt-get install postgresql postgresql-contrib -y

The default PostgreSQL user is ``postgres``.

Set the default PostgreSQL user password:

.. code-block:: bash

    sudo -u postgres psql postgres
    \password postgres

Hit enter, and a prompt to type a new password will show up.

Type ``\q`` to exit the postgres console, once you type a new password.

3. Install PHP-FPM 7.2
""""""""""""""""""""""

Let’s install PHP-FPM 7.2 with all the required extensions:

.. code-block:: bash

    sudo apt install software-properties-common
    sudo LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
    sudo apt update
    sudo apt install -y php7.2-fpm php7.2-pgsql php7.2-gd php7.2-xml \
    php7.2-intl php7.2-zip php7.2-mbstring php7.2-curl php7.2-bcmath

4. Configure PHP-FPM 7.2
""""""""""""""""""""""""

Run command:

.. code-block:: bash

    cd /etc/php/7.2/fpm/pool.d/ &&
    sudo curl -s -O https://gist.githubusercontent.com/takeit/2ee16ee50878eeab01a7ca11b69dec10/raw/e9eda2801ac3657495374fcb846c2ff101a3e070/www.conf
    && sudo service php7.2-fpm restart

5. Install Nginx server
"""""""""""""""""""""""

Run command:

.. code-block:: bash

    sudo apt-get -y install nginx

6. Configure Nginx server
"""""""""""""""""""""""""

Run command:

.. code-block:: bash

    cd /etc/nginx/sites-enabled/
    && sudo curl -s -O https://gist.githubusercontent.com/takeit/9c895b4d59930a9b550a43a0d26c0e0e/raw/bff973443d244929c8deda70f97b4ae862d9158b/default
    && sudo service nginx restart

7. Install RabbitMQ server
""""""""""""""""""""""""""

Run command:

.. code-block:: bash

    sudo apt install -y rabbitmq-server

8. Install Supervisor
"""""""""""""""""""""

Run command:

.. code-block:: bash

    sudo apt-get install -y supervisor

Before starting the installation make sure your server meets all the requirements listed above.

Completing the Superdesk Publisher installation
-----------------------------------------------

The Superdesk Publisher `repository`_ can be found on GitHub.

From there the source code can be downloaded and the Superdesk Publisher can be installed on your server.

Follow the guide below.

Assumed our server has ``192.168.0.102`` IP address.
You can change it to your own IP or domain name. But in this guide we will use ``192.168.0.102`` IP for Superdesk Publisher instance. 
Superdesk instance will run using ``192.168.0.101`` IP address.

1. Install Composer
-------------------

.. code-block:: bash

    cd ~/
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer

2. Download the source code
---------------------------

The default directory where the Publisher source code will be downloaded can be ``/var/www/publisher`` and all console commands
need to be executed inside that directory starting from now on.

Run commands in your terminal:

.. code-block:: bash

    cd /var/www/ && sudo git clone https://github.com/superdesk/web-publisher.git publisher

Install Superdesk Publisher source code dependencies:

.. code-block:: bash

    HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
    && sudo chown -R "$HTTPDUSER":"$HTTPDUSER" publisher/ && cd publisher
    && sudo -u www-data SYMFONY_ENV=prod composer install --no-dev --optimize-autoloader

All the source code dependencies will start to install.
Once it is done, you will be asked to fill the ``parameters.yml`` file which needs to be completed before proceeding.

If you don’t know what to set, just simply use default values by hitting “enter”
and replace the content of ``/var/www/publisher/app/config/parameters.yml`` file with:

.. code-block:: yaml

    # This file is auto-generated during the composer install
    parameters:
        env(DATABASE_HOST): 127.0.0.1
        env(DATABASE_PORT): null
        env(DATABASE_NAME): publisher
        env(DATABASE_USER): postgres
        env(DATABASE_PASSWORD): postgres
        env(DATABASE_SERVER_VERSION): 9
        mailer_transport: smtp
        mailer_host: 127.0.0.1
        mailer_user: null
        mailer_password: null
        env(SYMFONY_SECRET): SuperSecretTokenPleaseChangeIt
        swp_updater.version.class: SWP\Bundle\CoreBundle\Version\Version
        env(SWP_DOMAIN): 192.168.0.102 # server domain/IP where Superdesk Publisher is installed
        cache_servers:
            - 192.168.0.102 # server domain/IP where Superdesk Publisher is installed
        doctrine_cache_driver: array
        sentry.dsn: false
        session_memcached_host: localhost
        session_memcached_port: 11211
        session_memcached_prefix: sess
        session_memcached_expire: 3600
        allow_origin_cors: '*'
        superdesk_servers:
            - 192.168.0.101 # server domain/IP where Superdesk is installed
        env(ELASTICA_HOST): localhost
        env(ELASTICA_PORT): 9200
        env(RABBIT_MQ_HOST): 127.0.0.1
        env(RABBIT_MQ_PORT): 5672
        env(RABBIT_MQ_USER): guest
        env(RABBIT_MQ_PASSWORD): guest

And set proper permissions for ``cache`` and ``logs`` directories, run:

.. code-block:: bash

    sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX app/cache app/logs
    && sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX app/cache app/logs

3. Check Requirements
---------------------

Check if your server meets the requirements by running:

.. code-block:: bash

    php app/check.php

If everything is in order, you should see this message: ``Your system is ready to run Symfony projects on your screen.``

4. Create the Database and Update the Schema
--------------------------------------------

Inside ``/var/www/publisher`` directory, run the command to create the database:

.. code-block:: bash

    SYMFONY_ENV=prod php bin/console doctrine:database:create

And populate the database with the schema, run:

.. code-block:: bash

    SYMFONY_ENV=prod php bin/console doctrine:migrations:migrate --no-interaction

5. Create organization
----------------------

.. code-block:: bash

    SYMFONY_ENV=prod php bin/console swp:organization:create Publisher

6. Create tenant
----------------

.. code-block:: bash

    SYMFONY_ENV=prod php bin/console swp:tenant:create <organization_code> 192.168.0.102 Testing

Where ``<organization_code>`` is the organization code generated by the previous command and ``192.168.0.102`` is your IP/domain name
which points to the server where Superdesk Publisher is installed. Replace it with your and appropriate data.

7. Install theme
----------------

.. code-block:: bash

    sudo -u www-data SYMFONY_ENV=prod php bin/console swp:theme:install <tenant_code> src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f --activate -p

``<tenant_code>`` is the Tenant’s code generated by previous command. Replace it with the proper value.

**Install theme assets:**

.. code-block:: bash

    sudo -u www-data SYMFONY_ENV=prod php bin/console sylius:theme:assets:install

8. Run supervisor
-----------------

.. code-block:: bash

    sudo -u www-data SYMFONY_ENV=prod php bin/console rabbitmq-supervisor:build --env=prod


The Superdesk Publisher should be running and be accessible using your remote server IP, ``192.168.0.102`` in this case.

9. Clear the cache
------------------

Run command:

.. code-block:: bash

    SYMFONY_ENV=prod php bin/console cache:clear --env=prod

How to Configure Superdesk Publisher with Superdesk?
----------------------------------------------------

Now that the Superdesk and Superdesk Publisher applications are installed, it is possible to enable
Superdesk Publisher Component inside the Superdesk UI.

Superdesk Publisher Component is a JavaScript component that is a separate dependency
and can be included in Superdesk in order to manage Superdesk Publisher application.

The source code of this component can be found at `GitHub`_.

1. Update Configuration File
----------------------------

Login to the server where the Superdesk is installed.

Inside ``/opt/superdesk/client/dist`` directory on your server open the ``config.js``
and ``config.<hash>.js`` (e.g. ``config.23fr4.js``) files and override the content with the text as below:

.. code-block:: js

    window.superdeskConfig={
        apps: ['superdesk-publisher'],
        publisher: {
            protocol: "http",
            tenant: '', // subdomain
            domain: '192.168.0.102', // IP address or domain name of your server where Superdesk Publisher is installed
            base: 'api/v1'
        },
    };

That’s it! Now, when you log in to Superdesk in the left hamburger menu, you will see the Publisher menu item available:

.. image:: superdesk-publisher-menu.png
  :alt: Superdesk Publisher
  :align: center

2. Configure Subscriber to Publish Content from Superdesk to Superdesk Publisher
--------------------------------------------------------------------------------

You can read more about this in the official Superdesk Publisher `documentation`_.

Thank you for reading to the end of this post! If you liked what you saw, please give us a pat us on the back by starring our project on Github: https://github.com/superdesk/web-publisher.

.. _repository: https://github.com/superdesk/web-publisher
.. _here: https://github.com/superdesk/web-publisher#requirements
.. _GitHub: https://github.com/superdesk/superdesk-publisher
.. _documentation: http://superdesk-publisher.readthedocs.io/en/latest/manual/getting_started/superdesk-configuration.html#publish
