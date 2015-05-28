Themes
===============

Overview
--------

Superdesk Web Publisher themes system is build on top of fast, flexible and easy to use Twig templates. 

Themes are located under :doc:`app/Resources/themes` directory. Theme can provide templates used for customization of rendered site.

Basic theme must have following structure:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        templates/                  <=== Pages directory
            home.html.twig
        layouts/                    <=== Layouts directory
            default.html.twig
        assets/                     <=== Assets directory
            css/
            js/
            images/
        theme.yml                   <=== Theme configuration

Superdesk Web Publisher provides easy way to create device specific templates. This solution gives you oportunity to put in your templates only needed and realy used on that device type elements. 

Suported device types: :doc:`desktop, phone, tablet, plain`.

Device type specific theme would look like this:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        desktop                     <=== Templates used on desktops
            templates/                  <=== Pages directory
                home.html.twig
            layouts/                    <=== Layouts directory
                default.html.twig
        tablet                      <=== Templates used on tablets
            templates/                  <=== Templates directory
                home.html.twig
            layouts/                    <=== Layouts directory
                default.html.twig
        templates/                  <=== Default templates directory
            home.html.twig
        layouts/                    <=== Default layouts directory
            default.html.twig 
        assets/                     <=== Default assets directory
            css/
            js/
            images/
        theme.yml                   <=== Theme configuration


.. note::

    If device will not be recognized by system then it will fallback to :doc:`desktop` type. If there is no :doc:`deskopt` direcotry with required template file then system will try to load template from default (root level) directories.
