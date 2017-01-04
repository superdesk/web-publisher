Themes
======

Structure
---------

Themes provide templates for the customization of the appearance and functionality of your public-facing websites. Themes can also be translated to support multiple written languages and regional dialects.

The Superdesk Web Publisher themes system is built on top of fast, flexible and easy to use `Twig <http://twig.sensiolabs.org/>`_ templates.

By default, themes are located under the :code:`app/themes` directory. A basic theme must have the following structure:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        views/                  <=== Views directory
            home.html.twig
        translations/           <=== Translations directory
            messages.en.xlf
            messages.de.xlf
        public/                 <=== Assets directory
            css/
            js/
            images/
        theme.json              <=== Theme configuration

Web Publisher does not support the option of `Sylius theme structure`_ to have bundle resources nested inside a theme.

Multitenancy
------------

Superdesk Web Publisher can serve many websites from one instance, and each website *tenant* can have multiple themes. The active theme for the tenant can be selected with a local settings file, or by API.

If only one tenant is used, which is the default, the theme should be placed under the :code:`app/themes/default` directory, (e.g. :code:`app/themes/default/ExampleTheme`).

If there were another tenant configured, for example :code:`client1`, the files for one of this tenant's themes could be placed under the :code:`app/themes/client1/ExampleTheme` directory.

Device-specific templates
-------------------------

Superdesk Web Publisher provides an easy way to create device-specific templates. This means you only need to put the elements in a particular template which are going to be used on the target device.

The supported device types are: :code:`desktop, phone, tablet, plain`.

A theme with device-specific templates could be structured like this:

.. code-block:: bash

    ExampleTheme/                   <=== Theme starts here
        phone                       <=== Views used on phones
            views/                  <=== Views directory
                home.html.twig
        tablet                      <=== Views used on tablets
            views/                  <=== Views directory
                home.html.twig
        views/                      <=== Default templates directory
            home.html.twig
        translations/               <=== Translations directory
            messages.en.xlf
            messages.de.xlf
        public/                     <=== Assets directory
            css/
            js/
            images/
        theme.json                  <=== Theme configuration


.. note::

     If a device is not recognized by the Web Publisher, it will fall back to the :code:`desktop` type. If there is no :code:`desktop` directory with the required template file, the locator will try to load the template from the root level :code:`views` directory.

     More details about theme structure and configuration can be found in the `Sylius Theme Bundle documentation`_.

.. _Sylius Theme Bundle documentation: http://docs.sylius.org/en/latest/bundles/SyliusThemeBundle/your_first_theme.html

.. _Sylius Theme structure: http://docs.sylius.org/en/latest/bundles/SyliusThemeBundle/your_first_theme.html#theme-structure


Assets
------

To install theme assets you need to run ``sylius:theme:assets:install`` command.

Theme assets (JavaScript, CSS etc. files) should be placed inside the theme directory. There are few ways of reading
theme assets in your Twig templates. The below how-to describes where to place the assets, how to install it and use it.

Load assets from the theme's public directory (``app/themes/<theme-name>/public``)
``````````````````````````````````````````````````````````````````````````````````


1. Put the ``example.css`` asset file inside ``<theme-name>/public/css/`` directory.
2. Install assets by running command: ``php app/console sylius:theme:assets:install``.
3. Make use of the asset file in twig templates:

.. code-block:: twig

    <!-- loads test.css file directly /public/css/ in theme directory -->
    <link rel="stylesheet" href="{{ asset('theme/css/example.css') }}" />

Load assets from the public ``web`` directory
`````````````````````````````````````````````

1. Put the ``example.css`` asset file directly inside ``web`` directory.
2. Make use of the asset file in twig templates:

.. code-block:: twig

    <!-- loads asset file directly from `web` dir (`web/example.css`) -->
    <link rel="stylesheet" href="{{ asset('example.css') }}" />

Generate simple links for current theme assets
``````````````````````````````````````````````

If You need to get link to asset from outside of twig template then you can use this url:

.. code-block:: twig

    /public/{filePath}

    ex. <link rel="stylesheet" href="/public/css/example.css" />

Where {filePath} is path for your file from public directory inside theme.

Load Service Worker files (from domain root level)
``````````````````````````````````````````````````

If You want to use service worker or manifest file (it must be placed in root level) then you can use this url:

.. code-block:: twig

    /{fileName}.{fileExtension}

    ex. <link rel="manifest" href="/manifest.json">

Where {fileName} can be only :code:`sw` or :code:`manifest`.


Load bundles' assets
````````````````````

1. Install Symfony assets by running command: ``php app/console assets:install``.
2. Make use of the asset file in twig templates:

.. code-block:: twig

    <!-- loads bundle's asset file from bundles dir -->
    <link rel="stylesheet" href="{{ asset('bundles/framework/css/body.css') }}" />

Override bundles' assets from the theme
```````````````````````````````````````

There is a possibility to override bundle specific assets. For example, you have ``AcmeDemoBundle`` registered in your project.
Let's assume there is a ``body.css`` file placed inside this bundle (``Resources/public/css/body.css``).
To override ``body.css`` file from your theme, you need to place your new ``body.css`` file inside ``app/themes/<theme-name>/AcmeDemoBundle/public`` directory:

1. Put the ``body.css`` asset file inside ``app/themes/<theme-name>/AcmeDemoBundle/public`` directory.
2. Install assets by running command: ``php app/console sylius:theme:assets:install``.
3. Make use of the asset file in twig templates:

.. code-block:: twig

    <link rel="stylesheet" href="{{ asset('theme/acmedemo/css/body.css') }}" />


.. note::

    ``theme`` prefix in ``{{ asset('theme/css/example.css') }}`` indicates that the asset refers to current theme.

Translations
------------

The Symfony Translation component supports a variety of file formats for translation files, but in accordance with `best practices suggested in the Symfony documentation <https://symfony.com/doc/current/best_practices/i18n.html>`_, the XLIFF file format is preferred.
`JMSTranslationBundle <http://jmsyst.com/bundles/JMSTranslationBundle>`_ has been added to the project to facilitate the creation and updating of such files.

The use of abstract keys such as :code:`index.welcome.title` is preferred, with an accompanying description :code:`desc` in English to inform a translator what needs to be translated.
This description could simply be the English text which is to be displayed, but additional information about context could be provided to help a translator.

Abstract keys are used for two main reasons:

 #. Translation messages are mostly written by developers, and changes might be necessitated later. These changes would then result in changes for all supported languages instead of only for the source language, and some translations might be lost in the process.
 #. Some words in English are spelled differently in other languages, depending on their meaning, so providing context is important.

Here is an example of the preferred syntax in twig templates:

.. code-block:: twig

	{{ 'index.welcome.title'|trans|desc('Welcome to Default Theme!') }}

Translation labels added to Twig and php files can be extracted and added to XLIFF files using a `console command <http://jmsyst.com/bundles/JMSTranslationBundle/master/usage>`_ :code:`app/console translation:extract`.
This command can be used to create or update a XLIFF file in the locale :code:`en` for the :code:`DefaultTheme` of the FixturesBundle:

.. code-block:: bash

	app/console translation:extract en --dir=./src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ --output-dir=./src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/translations

This will create or update a XLIFF file in English called :code:`messages.en.xlf`, which can be used with a translation tool.

AMP HTML Integration
--------------------

`Google AMP HTML <https://www.ampproject.org/>`_ integration comes with Superdesk Publisher out of the box.
This integration gives you a lot of features provided by Google. To name a few: fast loading time and accessibility via Google engines etc. There is no need to install any dependencies, all you need to do is to create AMP HTML compatible theme or use the `default one <https://github.com/superdesk/web-publisher/tree/master/src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/amp/amp-theme>`_ provided by us.

Default AMP HTML theme is bundled in our main Demo Theme and can be installed using ``php app/console swp:theme:install`` command.

You could also copy it to your own main theme and adjust it in a way you wish.

.. note::

    See :ref:`setting-up-demo-theme` section for more details on how to install demo theme.

How to create AMP HTML theme?
`````````````````````````````

You can find more info about it in `AMP HTML official documentation <https://www.ampproject.org/docs/get_started/create>`_.

Where to upload AMP HTML theme?
```````````````````````````````

Publisher expects to load AMP HTML theme from main theme directory which is ``app/themes/<tenant_code>/<theme_name>``.
AMP HTML theme should be placed in ``app/themes/<tenant_code>/<theme_name>/amp/amp-theme`` folder.
``index.html.twig`` is the starting template for that theme. If that template doesn't exist, theme won't be loaded.
Once the theme is placed in a proper directory, it will be automatically loaded.

To test if the theme has been loaded properly you can access your article at e.g.: ``https://example.com/news/my-articles?amp``.

Linking AMP page and non-AMP page
`````````````````````````````````

To add a link to AMP page from article template in the form of ``<link>`` tags (which is required by AMP HTML integration for discovery and distribution), you can use ``amp`` Twig filter:

.. code-block:: twig

    {# app/themes/<tenant_code>/<theme_name>/views/article.html.twig #}
    <link rel="amphtml" href="{{ url(gimme.article)|amp }}"> {# https://example.com/news/my-articles?amp #}

And from AMP page:

.. code-block:: twig

    {# app/themes/<tenant_code>/<theme_name>/amp/amp-theme/index.html.twig #}
    <link rel="canonical" href="{{ url(gimme.article) }}"> {# https://example.com/news/my-articles #}



Theme Development Workflow
--------------------------

Theme workflow is based on `Gulp <http://gulpjs.com/>`_ task automation tool.
There are two main tasks you would use:

- gulp watch
- gulp build

Watch task is used for development. Run this task when playing with theme css or js.
Build task should be fired every time before you deploy changes. It minifies your assets and generates service worker.

.. note::

    Please use  `Publisher Theme Starter <https://github.com/tomaszrondio/gulp-service-worker-publisher-setup>`_

Service Worker
```````````````
A service worker is a script that your browser runs in the background, separate from a web page, opening the door to features that don't need a web page or user interaction. We use service worker to precache static assets and create runtime cache for images, articles and sections (optional). Goal is to decrease loading time of your page as much as possible.

To learn more about cache and service workers debugging please read:
`Google: Progressive Web Apps <https://developers.google.com/web/tools/chrome-devtools/progressive-web-apps>`_ ,
`Google: manage data <https://developers.google.com/web/tools/chrome-devtools/manage-data/local-storage>`_

How to install
```````````````

If you go with Theme Starter all you have to do is run *gulp build* command. It will generate proper service worker for you.

File structure:

- /public/js/scripts/service-worker-registration.js - registers service worker in browser
- /public/sw.html - service worker registration for `AMP <https://www.ampproject.org/>`_ pages
- /public/sw.js - actual service worker file

Precache
```````````````````````````````
Precaching static assets in browser will save server requests. We want to precache css, javascript as well as images and fonts.

All you have to do is to put files in proper folders: */public/dist*,*/public/img* or */public/fonts*. Gulp will do the rest.

If you want to configure it yourself, search gulpfile.js for

.. code-block:: js

    staticFileGlobs: ['public/dist/**/*.{js,html,css,png,jpg,gif,svg,eot,ttf,woff}','public/img/**/*.{js,html,css,png,jpg,gif,svg,eot,ttf,woff}', 'public/fonts/**/*.{svg,eot,ttf,woff}']

Runtime Caching
````````````````

By default it is set up to cache fonts and scripts from googleapis as well as images and other media files from *domain.com/media/** url.

That is the part of gulpfile that generates service worker code responsible for runtime caching:

.. code-block:: js

    runtimeCaching: [{
        // matches googleapis cdn's. Fonts, jquery etc.
        urlPattern: /(googleapis|gstatic)/,
        handler: 'cacheFirst',
        options: {
          cache: {
            maxEntries: 200,
            name: 'googleapis-cache'
          }
        }
      },
      {
        // matches media content from publisher. Images etc.
        urlPattern: /(media)/,
        handler: 'cacheFirst',
        options: {
          cache: {
            maxEntries: 50,
            name: 'media-cache'
          }
        }
      }
      ],

Advanced Runtime Caching
`````````````````````````
You can handle requests by your self and add your own runtime caching strategy. To enable that uncomment following line in gulpfile:

.. code-block:: js

    importScripts: ['/public/sw-contentCaching.js']

You can communicate between service worker and web page both ways (starter supports one way communication). It allows you to cache articles that are listed on homepage/section pages, cache pages when visited, add "save to offline" button etc. Sky is the limit.

.. note::

    Be aware of what you are doing. In some cases it might be harmful and even break your page!

Follow example code in */public/sw-contentCaching.js* (service worker part) and */public/js/contentCaching.js* (web page part)
