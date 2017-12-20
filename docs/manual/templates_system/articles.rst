Handling Articles
=================

Listing Articles
----------------

Publisher have concept of Meta Loaders - one of built in loaders covers articles.

The :code:`articles` loader parameters:

 * (optional) key :code:`route` - id or name or array of id's used for loading meta (if omitted then current route is used).

.. code-block:: twig

    {% gimmelist article from articles %} <!-- It will use route from context -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

.. code-block:: twig

    {% gimmelist article from articles with {'route': 1} %} <!-- route id -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

.. code-block:: twig

    {% gimmelist article from articles with {'route': '/news'} %} <!-- route name -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

.. code-block:: twig

    {% gimmelist article from articles with {'route': [1, 2, 3]} %} <!-- array with routes id -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

 * (optional) key :code:`metadata` - It matches article’s metadata, and you can use all metadata fields that are defined for the article, i.e.: language, located etc.

.. code-block:: twig

    {% gimmelist article from articles with {'metadata':{'language':'en'}} %}
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

* (optional) key :code:`keywords` - It matches article’s keywords,

    {% gimmelist article from articles with {'keywords':['keyword1', 'keyword2']} %}
        <img src="{{ url(article) }}" />
    {% endgimmelist %}
