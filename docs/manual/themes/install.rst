Create or install a theme
-------------------------

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
