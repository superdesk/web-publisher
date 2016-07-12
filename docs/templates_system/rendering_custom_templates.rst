Render custom templates
=======================

Routes and articles have option to change template name used for output rendering. Default template name is ``article.html.twig``.

How to change Route/Article template name?
------------------------------------------

You can change default template name values for article and route with API calls (with providing it on resource create or resource update calls).

Example resource update calls:

article:
````````

.. code-block:: bash

    curl -X "PATCH" -d "article[template_name]=new_template_name.html.twig" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/content/articles/features

route:
``````

.. code-block:: bash

    curl -X "PATCH" -d "route[template_name]=custom_route_template.html.twig" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/content/routes/news
