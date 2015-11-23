Fixtures Bundle
================

Overview
--------

Fixtures Bundle helps developers to create fixtures/fake data that can be
used for the development or/and testing purposes.

It relies on the following 3rd party bundles:

-  `DoctrineFixturesBundle`_ (gives possibility to load data fixtures programmatically into the Doctrine ORM or ODM)
-  `AliceBundle`_ (bundle to manage fixtures with nelmio/alice and fzaninotto/Faker):
    - `fzaninotto/Faker`_ (generates fake data for you)
    - `nelmio/alice`_ (It gives you a few essential tools to make it very easy to generate complex data with constraints in a readable and easy to edit way)

It also gives the possibility to setup, ready to use demo theme, needed
for the development and shows how to use fixtures in PHPUnit tests.

How to use it
==============

The following chapter describes how to make use of the Fixtures Bundle features.

Bundle configuration
-----------------------------------

Add the below configuration to your ``config_dev.yml`` file:

.. code:: yaml

    # app/config/config_dev.yml
    hautelook_alice:
        db_drivers:
            orm: ~          # Enable Doctrine ORM if is registered
            phpcr: ~        # Enable Doctrine PHPCR ODM if is registered
        locale: en_US       # Locale to used for faker; must be a valid Faker locale otherwise will fallback to en_EN
        seed: 1             # A seed to make sure faker generates data consistently across runs, set to null to disable
        persist_once: false # Only persist objects once if multiple files are passed

To your ``AppKernel.php`` add:

.. code:: php

    // app/AppKernel.php
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
          ...
          $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
          $bundles[] = new SWP\FixturesBundle\SWPFixturesBundle();
          ...
    }

Creating a simple Alice fixture (YAML format)
---------------------------------------------

Fixtures should be created inside
``SWPFixturesBundle/DataFixtures/<db_driver>/<environment>``, where
``<environment>`` is the current environment name (Dev, Test) and
``<db_driver>`` is the database driver.

Replace ``db_driver`` either with ``ORM`` for the Doctrine ORM or
``PHPCR`` for the Doctrine PHPCR ODM.

Example Alice fixture:

.. code-block:: yaml

    SWP\ContentBundle\Model\Page:
        page1:
            name: "About Us"
            type: 1
            slug: "about-us"
            templateName: "static.html.twig"
            contentPath: "/swp/content/about-us"
        page2:
            name: "Features"
            type: 1
            slug: "features"
            templateName: "features.html.twig"
            contentPath: "/swp/content/features"
        page3:
            name: "Get Involved" # we can also use faker library formatters like: <paragraph(20)> etc
            type: 1
            slug: "get-involved"
            templateName: "involved.html.twig"
            contentPath: "/swp/content/get-involved"
            parent: "@page1"

The above configuration states that we want to persist into database,
three objects of type ``SWP\ContentBundle\Model\Page``. We can use faker `formatters`_
where, for example, ``<paragraph(20)>`` is one of the
`fzaninotto/Faker`_ formatter, which tells Alice to generate 20
paragraphs filled with fake data.

For the convention, Alice YAML files should be placed inside
``FixturesBundle/DataFixtures/<db_driver>/<environment>``, where ``<environment>`` is the current environment name (Dev, Test) and
``<db_driver>`` is the database driver.

For instance, having ``FixturesBundle/DataFixtures/ORM/Test/page.yml`` Alice
fixture, we will be able to persist fake data defined in YAML file into
the databse (using Doctrine ORM driver), only when ``test`` environment
is set in ``AppKernel.php`` or when provided as a parameter in console command which loads the fixtures:

.. code:: bash

    $ php app/console h:d:f:l --env=test

Please, see `documentation`_ for more details about environment specific
fixtures.

There is a lot of flexibility on how to define fixtures, so it’s up to
developer how to create them.

For more details on how to create Alice fixtures, please read `here`_ as a reference.

Loading all fixtures
---------------------------------------------

**Note:** Remember to update your database schema before loading
fixtures! To do it, run in terminal the following commands:

.. code-block:: bash

    $ php app/console doctrine:schema:update --force
    $ php app/console doctrine:phpcr:repository:init

Once you have your fixtures defined, we can simply load them. To do that
you must execute console commands in terminal:

To load Doctrine ORM fixtures:

.. code:: bash

    $ php app/console h:d:f:l --append
    # see php app/console h:d:f:l --help for more details

To load Doctrine PHCR fixtures:

.. code:: bash

    $ php app/console h:d:phpcr:f:l --append
    # see php app/console h:d:phpcr:f:l --help for more details

After executing the above commands, your database will be filled with the
fake data, which can be used by themes.

Loading fixtures in PHPUnit tests
---------------------------------------------

Loading PHPCR fixtures:

.. code:: php

    $this->loadFixtureFiles([
       '@SWPFixturesBundle/DataFixtures/PHPCR/Test/article.yml',
    ], true, null, 'doctrine_phpcr');

Loading ORM fixtures:

.. code:: php

    $this->loadFixtureFiles([
       '@SWPFixturesBundle/DataFixtures/ORM/Test/page.yml',
       '@SWPFixturesBundle/DataFixtures/ORM/Test/pagecontent.yml',
    ]);

Setting up demo theme
---------------------------------------------

To make it easier to start with the WebPublisher, we have created a simple
demo theme. To set this theme as a active one, you need to execute the
following console command in terminal:

.. code:: bash

    $ php app/console theme:setup
    # see php app/console theme:setup --help for more details

.. _DoctrineFixturesBundle: https://github.com/doctrine/DoctrineFixturesBundle
.. _AliceBundle: https://github.com/hautelook/AliceBundle
.. _fzaninotto/Faker: https://github.com/fzaninotto/Faker
.. _nelmio/alice: https://github.com/nelmio/alice
.. _formatters: https://github.com/fzaninotto/Faker#formatters
.. _documentation: https://github.com/hautelook/AliceBundle/blob/master/src/Resources/doc/advanced-usage.md#environment-specific-fixtures
.. _here: https://github.com/nelmio/alice#table-of-contents
