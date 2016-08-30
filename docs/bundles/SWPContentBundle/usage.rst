Usage
=====

Rules to assign routes and/or templates to articles
---------------------------------------------------

With the api, it is possible to create and manage RouteToArticle entities in order to assign a route and/or a template to content received in a package from a provider based on the metadata in that package.

The rules themselves are to be written in Symfony's expression language, documentation for which can be found here: http://symfony.com/doc/current/components/expression_language/syntax.html

The article entity generated from the package can be referenced directly in the rule. So, here is an example of a rule:

.. code-block:: yaml

    'article.getMetadata("var_name") matches "/regexExp/"'

A priority can also be assigned to the rule. This is simply an integer. The rules are ordered by their priority (the greater the value, the higher the priority) before searching for the first one which matches.

The route to be assigned is identified by its id, for example:

.. code-block:: yaml

    'articles/features'

If a template name is given with the rule, this template will be assigned to the article instead of the one in the route.
