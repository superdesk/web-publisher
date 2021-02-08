Templates System
================

The Superdesk Publisher templates system has its own git repository, at: https://github.com/SuperdeskWebPublisher/templates-system

.. toctree::
   :maxdepth: 4
   :glob:

   twig
   page_templates
   custom_pages
   properties
   templates_features
   articles
   related_articles
   article_media
   article_author
   slideshow
   routes
   content_list
   keywords
   templates_caching
   search
   tips


Templates inheritance
---------------------

Default template name for route and articles in Publisher is :code:`article.html.twig`.

**Inheritance overview:**

.. code-block:: bash

    > article.html.twig
        > Route custom template
            > Article custom template

If :code:`route` is collection type then it can have declared two default templates:

 * :code:`default_template` used for rendering Route content (eg. /sport).
 * :code:`default_articles_template` used for rendering content attached to this route (eg. /sport/usain-bolt-fastest-man-in-theo-world).

.. note::

    When route :code:`default_template` property is set but not :code:`default_articles_template`, then Web Publisher will load all articles attached to this route with template chosen in :code:`default_template` (not with :code:`article.html.twig`).

If :code:`content` have assigned custom template then it will always override other already set templates.

How to change the Route/Article template name?
----------------------------------------------

You can change default template name values for article and route either in *routes management*, or with API calls (providing it on resource create or resource update calls).

Example resource update calls:

article:
````````

.. code-block:: bash

    curl -X "PATCH" -d "article[template_name]=new_template_name.html.twig" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/content/articles/features

route:
``````

.. code-block:: bash

    curl -X "PATCH" -d "route[template_name]=custom_route_template.html.twig" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/content/routes/news
