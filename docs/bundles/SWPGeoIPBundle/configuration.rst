Configuration Reference
=======================

The SWPGeoIPBundle can be configured under the ``swp_geo_ip`` key in your configuration file.
This section describes the whole bundle's configuration.

Full Default Configuration
--------------------------

.. code-block:: yaml

        # app/config/config.yml
        swp_geo_ip:
            database_url: 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz'
            database_path: '%kernel.cache_dir%/GeoLite2-City.mmdb'


``database_url``
****************

**type**: ``string`` **default**: ``https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz``

GeoIP2 database URL.

``database_path``
****************

**type**: ``string`` **default**: ``%kernel.cache_dir%/GeoLite2-City.mmdb``

The path to the downloaded GeoIP2 database.
