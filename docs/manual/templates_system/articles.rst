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

    {% gimmelist article from articles with {'route': '/news'} %} <!-- route staticPrefix -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

.. code-block:: twig

    {% gimmelist article from articles with {'route': ['/news', '/sport/*']} %} <!-- route staticPrefix -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

.. note::

   :code:`'/sport/*'` syntax will load articles from main route (:code:`'/sport'`) and all of it 1st level children (eg. :code:`'/sport/football'`).

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

.. code-block:: twig

    {% gimmelist article from articles with {'keywords':['keyword1', 'keyword2']} %}
        <img src="{{ url(article) }}" />
    {% endgimmelist %}


* Filtering out selected articles (useful when you want to exclude articles listed already in content list)

.. code-block:: twig

    {% gimmelist article from articles without {article:[1,2]} %} <!-- pass articles ids (collected before) -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

.. code-block:: twig

    {% gimmelist article from articles without {article:[gimme.article]} %} <!-- pass articles meta objects -->
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

* Ordering by article comments count (set by external system)

.. code-block:: twig

    {% gimmelist article from articles|order('commentsCount', 'desc') %}
        <img src="{{ url(article) }}" />
    {% endgimmelist %}

Fetching first article url (when it's changed after slug or route change)
-------------------------------------------------------------------------

.. code-block:: twig

    {% gimme article with {slug: "test-article"} %}
        <a href="{{ original_url(article) }}">{{ article.title }}</a>
    {% endgimme %}

.. note::

   :code:`original_url` function will always return valid article url. If article url was changed after publication, then it will return it's original (first) value.
