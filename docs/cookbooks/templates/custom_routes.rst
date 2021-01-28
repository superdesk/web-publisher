Create new custom route
=======================

Our use case will be article authors pages. As we don't want to have special route for every author - we need to create one witch will fit all our needs.

We need url like that: :code:`publisher.dev/authors/{authorSlug}`. Author slug is a parameter passed to template and will be used by us for loading desired author. Example filled route: :code:`publisher.dev/authors/john-doe`.

We can create route like that with API:

:code:`POST /api/v1/content/routes/`

.. code-block:: json

    {
        "name": "Authors",
        "slug": "authors",
        "type": "custom",
        "templateName": "author.html.twig",
        "variablePattern": "/{authorSlug}",
        "requirements": [
            {
                "key": "authorSlug",
                "value": "[a-zA-Z\\-_]+"
            }
        ]
    }

Important parts:

* type :code:`custom`: says to publisher that we want to set :code:`variablePattern` and :code:`requirements` manually.
* :code:`variablePattern` - string with set parameters placeholders added to end of route.
* :code:`requirements` = array of objects with regular expression for provided parameters.


Now in template :code:`author.html.twig` we can load author by slug provided in url.

.. code-block:: twig

    {% gimme author with { slug: app.request.attributes.get('authorSlug') } %}
        Author name: {{ author.name }}
    {% endgimme %}

And done - you have custom route with option to pass parameters in url and use them later in template.


