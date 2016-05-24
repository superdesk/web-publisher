Themes
===============

Overview
--------

Superdesk Web Publisher themes system is build on top of fast, flexible and easy to use Twig templates.

By default, themes are located under the :code:`app/themes` directory. Theme can provide templates used for customization of rendered site.

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
        theme.json                   <=== Theme configuration file

If only one tenant is used (which is like that by default), theme should be placed under the :code:`app/themes/default` directory. (e.g. :code:`app/themes/default/ExampleTheme`).

If there were another tenant configured, for example :code:`app/themes/client1`, the themes for this given tenant should be placed under the :code:`app/themes/client1/ExampleTheme` path.

Superdesk Web Publisher provides an easy way to create device specific templates. This solution gives you opportunity to put in your templates only needed and really used on that device type elements.

Supported device types: :code:`desktop, phone, tablet, plain`.

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
        theme.json                   <=== Theme configuration


 .. note::

     If device will not be recognized by system then it will fallback to :code:`desktop` type. If there is no :code:`desktop` directory with required template file then locator will try to load template from root level :code:`views` directory.

     For more details about the themes structure and configuration can be found `here`_.

.. _here: http://docs.sylius.org/en/latest/bundles/SyliusThemeBundle/your_first_theme.html
