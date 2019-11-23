Installing Superdesk Publisher
==============================

This guide describes how to install Superdesk Publisher (refered to as Publisher) on an Ubuntu 18.04 server using Nginx web server. 
This guide was verified as accurate and tested using Superdesk Publisher 2.0.3.

Installing Publisher Prerequisites
----------------------------------

See the `Publisher Requirements`_ to read more about specific requirements.

Before starting, make sure your Ubuntu server has the latest packages available by running the commands:

.. code-block:: bash

	sudo apt update

	sudo apt upgrade

Install PHP-FPM 7.3 and Extensions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add the ``ondrej/php`` repository, which has the PHP 7.3 package and required extensions:

.. code-block:: bash

	#!/bin/bash

	sudo apt install software-properties-common
	sudo add-apt-repository ppa:ondrej/php
	sudo apt update

Install PHP 7.3 and required extensions:

.. code-block:: bash

	sudo apt-get install -y php7.3-fpm php7.3-pgsql php7.3-gd php7.3-xml php7.3-intl php7.3-zip php7.3-mbstring php7.3-curl php7.3-bcmath

Configure PHP-FPM 7.3 by running the command:

.. code-block:: bash

	cd /etc/php/7.3/fpm/pool.d/ &&
	sudo curl -s -O https://gist.githubusercontent.com/takeit/2ee16ee50878eeab01a7ca11b69dec10/raw/e9eda2801ac3657495374fcb846c2ff101a3e070/www.conf &&
	sudo service php7.3-fpm restart

Install PostgreSQL
~~~~~~~~~~~~~~~~~~

Install PostgreSQL:

.. code-block:: bash

    sudo apt-get install postgresql postgresql-contrib -y

The default PostgreSQL user is ``postgres`` with no password set.

Install Memcached
~~~~~~~~~~~~~~~~~

Install Memcached:

.. code-block:: bash

	sudo apt-get install -y memcached

Install the Memcached PHP extension:

.. code-block:: bash

	sudo apt-get install -y php7.3-memcached

Install ElasticSearch
~~~~~~~~~~~~~~~~~~~~~

ElasticSearch v5.6 will be used. Run the following command to install ES:

.. code-block:: bash

    curl -L -O https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-5.6.0.deb &&
    sudo dpkg -i elasticsearch-5.6.0.deb && sudo apt-get -y update &&
    sudo apt-get -y install --no-install-recommends openjdk-8-jre-headless &&
    sudo systemctl enable elasticsearch && sudo systemctl restart elasticsearch

The ElasticSearch should be running on port ``9200``. You can run the following command to verify this:

.. code-block:: bash

    curl -s "http://localhost:9200"

If you get no response in the console after running that command, use this command to check
for error messages:

.. code-block:: bash

	systemctl status elasticsearch

Install and Configure Nginx Server
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Install Nginx:

.. code-block:: bash

	sudo apt-get -y install nginx

Configure Nginx ``site-enabled`` by editing the file ``/etc/nginx/sites-enabled/default``. Paste in the following
configuration:

.. code-block::

	server {
		server_name example.com
		listen 80 default;
		root /var/www/publisher/public;

		location / {
			try_files $uri /index.php$is_args$args;
		}

		location ~ ^/index\.php(/|$) {
			fastcgi_pass 127.0.0.1:9000;
			fastcgi_split_path_info ^(.+\.php)(/.*)$;
			include fastcgi_params;

			fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
			fastcgi_param DOCUMENT_ROOT $realpath_root;

			internal;
		}

		location ~ \.php$ {
			return 404;
		}

		error_log /var/log/nginx/project_error.log;
		access_log /var/log/nginx/project_access.log;
	}

Restart the Nginx service:

.. code-block:: bash

	sudo service nginx restart

Install RabbitMQ Server
~~~~~~~~~~~~~~~~~~~~~~~

Install RabbitMQ:

.. code-block:: bash

	sudo apt install -y rabbitmq-server

Install the AMQP PHP extension:

.. code-block:: bash

	sudo apt-get install -y php7.3-amqp

Install Supervisor
~~~~~~~~~~~~~~~~~~

Install Supervisor:

.. code-block:: bash

	sudo apt-get install -y supervisor

Installing Publisher
--------------------

Clone the source code from the Publisher repository on GitHub, then install dependencies and
configure the Publisher server.

Clone the Publisher Repository
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The default directory where the Publisher source code will be downloaded can be ``/var/www/publisher`` and all console commands
need to be executed inside that directory starting from now on.

Run the clone command in your terminal:

.. code-block:: bash

    cd /var/www/ && sudo git clone https://github.com/superdesk/web-publisher.git publisher && cd publisher

All commands must be run in the ``/var/www/publisher`` directory from now on.

Install Publisher Dependencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Install Composer:

.. code-block:: bash

	sudo apt-get install composer -y

Install Publisher's dependencies (which can be found in ``composer.json``) using the following command:

.. code-block:: bash

	composer install

Create and Populate the Database
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Create a new terminal session and log into the postgres user:

.. code-block:: bash

	su - postgres

Create a new user 'root' as a superuser to match Publisher's default database connection configuration:

.. code-block:: bash

	createuser -s -d root

Next, find the location of PostgreSQL's ``pg_hba.conf`` file:

.. code-block:: bash

	psql -t -P format=unaligned -c 'show hba_file';

In this guide we are using version 10 of PostgreSQL, so our ``pg_hba.conf`` is located at
``/etc/postgresql/10/main/pg_hba.conf``. Edit this file and change the local connections authentication method
from ``peer`` or ``md5`` to ``trust``.

.. DANGER::

	Changing this setting to trust will allow anyone, even remote, to be able to log into the database as any 
	user without authentication. You will learn how to secure PostgreSQL in `Configure and secure your Publisher server`_.

Now, reload the ``pg_hba.conf`` file:

.. code-block:: bash

	psql -t -P format=unaligned -c 'select pg_reload_conf()';

Exit the postgres user session:

.. code-block:: bash

	exit

Create the database using Doctrine:

.. code-block:: bash

	php bin/console doctrine:database:create

Populate the database schema:

.. code-block:: bash

	php bin/console doctrine:migrations:migrate

If you're not installing Publisher for a production environment and want to quickly add test data, populate the database with test 
data using the following command:

.. code-block:: bash

	php bin/console doctrine:fixtures:load

or

.. code-block:: bash

	php -d memory_limit=-1 bin/console doctrine:fixtures:load

Generate the SSH keys to properly use the authentication (readers)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Generate the SSH keys:

.. code-block:: bash

	#!/bin/bash

	mkdir -p config/jwt
	openssl genrsa -out config/jwt/private.pem -aes256 4096
	openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

In case first openssl command forces you to input password use following to get the private key decrypted:

.. code-block:: bash

	#!/bin/bash

	openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
	mv config/jwt/private.pem config/jwt/private.pem-back
	mv config/jwt/private2.pem config/jwt/private.pem

Create an Organization and Tenant
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Create a new organization:

.. code-block:: bash

	php bin/console swp:organization:create Acme

An organization code will be output. Take note of it.

Create a new tenant under your organization:

.. code-block:: bash

	php bin/console swp:tenant:create <organization_code> example.com AcmeTenant

Take note of the tenant code output by this command.

Install the Demo Theme
~~~~~~~~~~~~~~~~~~~~~~

Install and activate the demo theme. Replace 123abc with your tenant code:

.. code-block:: bash

	php bin/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p -a

Install the theme assets (images, stylesheets, JavaScript, etc.):

.. code-block:: bash

	php bin/console sylius:theme:assets:install

Configure Supervisor
~~~~~~~~~~~~~~~~~~~~

Supervisor is used to automatically start services that Publisher depends on.

The program configuration files for Supervisor programs are founds in the 
``/etc/supervisor/conf.d`` directory, normally with one program per file and a ``.conf`` extension. 
We prepared ready-to-use configuration files for Publisher consumers. You can find them in 
``etc/scripts/supervisor`` directory.

Copy them to the Supervisor configs directory:

.. code-block:: bash

	cp -r etc/scripts/supervisor/. /etc/supervisor/conf.d

Then, reload Supervisor:

.. code-block:: bash

	systemctl reload supervisor

Bind websocket queue to websocket exchange:

.. code-block:: bash

	#!/bin/bash

	sudo rabbitmq-plugins enable rabbitmq_management
	wget http://127.0.0.1:15672/cli/rabbitmqadmin
	chmod +x rabbitmqadmin
	sudo mv rabbitmqadmin /etc/rabbitmq
	/etc/rabbitmq/rabbitmqadmin --vhost=/ declare binding source="swp_websocket_exchange" destination="swp_websocket"

Start the web server:

.. code-block:: bash

	php bin/console server:start

Use your web browser to navigate to your Publisher instance, using the domain you specified earlier when 
creating a new tenant. You should now see the home page for your tenant!

Next Steps
----------

- `Configure and secure your Publisher server`_
- `Connect Publisher with a Superdesk instance`_
- `Develop your own theme`_

.. _Publisher Requirements: https://github.com/superdesk/web-publisher#requirements
.. _Configure and secure your Publisher server: http://superdesk-publisher.readthedocs.io/en/latest/manual/getting_started/publisher-configuration.html
.. _Connect Publisher with a Superdesk instance: http://superdesk-publisher.readthedocs.io/en/latest/manual/getting_started/superdesk-superdesk-publisher-setup.html
.. _Develop your own theme: http://superdesk-publisher.readthedocs.io/en/latest/manual/getting_started/setting-up.html
