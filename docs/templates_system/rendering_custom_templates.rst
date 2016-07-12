Render custom templates
=======================

Routes and articles have the option to change the template name used for output rendering. The default template name is ``article.html.twig``.

How to change the Route/Article template name?
----------------------------------------------

You can change default template name values for article and route with API calls (providing it on resource create or resource update calls).

Example resource update calls:

article:
````````

.. code-block:: bash

    curl -X "PATCH" -d "article[template_name]=new_template_name.html.twig" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/content/articles/features

route:
``````

.. code-block:: bash

    curl -X "PATCH" -d "route[template_name]=custom_route_template.html.twig" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/content/routes/news
