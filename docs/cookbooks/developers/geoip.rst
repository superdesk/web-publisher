GeoIP
=====

GeoIP feature allows to restrict access to the specific articles based on the geolocation metadata.

How it works?
~~~~~~~~~~~~~

When this feature is enabled, the reader's IP address is read on the visited article page.
Then, the IP address is checked in the GeoIP2 database to check which country or state it comes from.
The GeoIP database has to be downloaded fist.
If the article place metadata matches the IP country or state, access to the articles is denied.

The GeoIP features are disabled by default.
To enable the GeoIP features ou have to set the ``GEO_IP_ENABLED`` environment variable to ``true`` (``GEO_IP_ENABLED=true``)
in your .env.local file.

Before enabling this feature, the GeoIP2 database must be downloaded:

.. code-block:: bash

  php bin/console swp:geoip:db:update

Executing the above command will download the GeoIP2 database to the cache dir. From this directory, the Publisher will read the GeoIP data.

Calls to the GeoIP database are cached by default.

Performance
~~~~~~~~~~~

To increase the performance it's recommended to install native PHP extension for GeoIP2.

MaxMind provides an optional C extension that is a drop-in replacement for MaxMind\Db\Reader. This will speed up the location lookups for GeoIp2 PHP provider enormously and is recommended for high traffic instances.

Installing libmaxminddb
-----------------------

The PHP extension requires the C library libmaxminddb for reading MaxmindDB (GeoIP2) files.

`Read how to install this library <https://github.com/maxmind/libmaxminddb/blob/master/README.md#installation>`_

Installing PHP Extension
------------------------

Download the `https://github.com/maxmind/MaxMind-DB-Reader-php <https://github.com/maxmind/MaxMind-DB-Reader-php>`_ repository
and in the top-level directory execute:

.. code-block:: bash

    cd ext
    phpize
    ./configure
    make
    sudo make install

Then add ``extension="maxminddb.so"`` into your ``php.ini`` config file.

The reads from the GeoIP2 database will be automatically faster.
