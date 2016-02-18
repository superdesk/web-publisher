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
