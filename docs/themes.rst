Themes
===============

Overview
--------

Superdesk Web Publisher themes system is build on top of fast, flexible and easy to use Twig templates.

Themes are located under :code:`app/Resources/themes` directory. Theme can provide templates used for customization of rendered site.

Basic theme must have following structure:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        views/                  <=== Views directory
            home.html.twig
        translations/           <=== Translations directory
            messages.en.yml
        public/                     <=== Assets directory
            css/
            js/
            images/
        composer.json                   <=== Theme configuration


Superdesk Web Publisher provides easy way to create device specific templates. This solution gives you opportunity to put in your templates only needed and really used on that device type elements.

Suported device types: :code:`desktop, phone, tablet, plain`.

Device type specific theme would look like this:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        phone                     <=== Views used on phones
            views/                  <=== Views directory
                home.html.twig
        tablet                      <=== Views used on tablets
            views/                  <=== Views directory
                home.html.twig
        views/                  <=== Default templates directory
            home.html.twig
        translations/           <=== Translations directory
            messages.en.yml
        public/                     <=== Assets directory
            css/
            js/
            images/
        composer.json                   <=== Theme configuration


 .. note::

     If device will not be recognized by system then it will fallback to :code:`desktop` type. If there is no :code:`desktop` directory with required template file then locator will try to load template from root level :code:`views` directory.
