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
