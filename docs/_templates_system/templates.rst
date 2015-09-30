.. _templates:

Templates
=========

Gimme and SWP custom Twig tags
------------------------------

Gimme allows you fetch nedded Meta object in any place of your template file. It supports single Meta objects (with :code:`gimme` ) and collections of Meta objects (with :code:`gimme_list`).

gimme
`````

Tag :code:`gimme` have one required parameter and one optional:

 * (required) Meta object type (and name of variable available inside block) ex.: *article*
 * (optional) Keword :code:`with` and parameters for Meta Loader ex.: :code:`{ param: "value" }`

.. code-block:: twig

    {% gimme article %}
        {# article Meta will be available under "article" variable inside block #}
        {{ article.title }}
    {% endgimme %}

Meta Loaders sometimes requires some special parameters - like article number, language, user id etc..

.. code-block:: twig

    {% gimme article with { articleNumber: 1 } %}
        {# Meta Loader will use provided parameters to load article Meta #}
        {{ article.title }}
    {% endgimme %}

gimmelist
`````````

Tag :code:`gimmelist` have two required parameter and two optional:

 * (required) Name of variable available inside block: :code:`article`
 * (required) Keword :code:`from` and type of requested Meta's in collection: :code:`from articles` with filters passed to Meta Loader as extra parameters (:code:`start`, :code:`limit`, :code:`order`)
 * (optional) Keword :code:`with` and parameters for Meta Loader ex.: :code:`with {foo: 'bar', param1: 'value1'}`
 * (optional) Keword :code:`if` and expresion used for results filtering

required parameters:

.. code-block:: twig

    {% gimmelist article from articles %}
        {{ article.title }}
    {% endgimmelist %}

all parameters:

.. code-block:: twig

    {% gimmelist article from articles|start(0)|limit(10)|order('id', 'desc')
        with {foo: 'bar', param1: 'value1'}
        if article.title == "New Article 1"
    %}
        {{ article.title }}
    {% endgimmelist %}

gimmeUrl
````````

Generate url for Meta object (if possible).

Function :code:`gimmeUrl` have one required parameter:

* {required} Meta object for witch you want generate url (so far we support only article)

example:

.. code-block:: twig

    {% gimmelist article from articles %}
        <a href="{{ gimmeUrl(article) }}">{{ article.title }}</a>
    {% endgimmelist %}



How to work with Meta objects
-----------------------------

On template level every variable in Context and fetched by :code:`gimme` and :code:`gimme_list` is representation of Meta objects.


**dump**

.. code-block:: twig

    {{ dump(article) }}

**print**

.. code-block:: twig

    {{ article }} - it meta configuration have filled to_string property then value of this property will be printed, json representation otherwise

**access property**

.. code-block:: twig

    {{ article.title }}
    {{ article['title']}}
