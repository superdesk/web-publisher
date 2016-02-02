.. _templates:

Templates
=========

Gimme and SWP custom Twig tags
------------------------------

Gimme allows you fetch needed Meta object in any place of your template file. It supports single Meta objects (with :code:`gimme` ) and collections of Meta objects (with :code:`gimmelist`).

container
`````````

Tag :code:`container` have one required and one optional parameters:

 * (required) container unique name ex.: *frontpage_sidebar*
 * (optional) keyword :code:`with` and default parameters for containers (they are used to create container on theme instalation).

.. code-block:: twig

     {% container 'frontpage_sidebar' with {
         'width': 400,
         'height': 500,
         'styles': 'border: solid 1px red',
         'class': 'css_class_name',
         'data': {'custom-key': value}
     }%}
     {% endcontainer %}

This container tag will render that html code:

.. code-block:: html

    <div id="frontpage_sidebar" class="swp_container css_class_name" style="width: 300px; height: 500px; border: solid 1px red;" data-custom-key="value"></div>

Available container parameters:
 * [integer] width - container width
 * [integer] height - container height
 * [string] styles - container inline styles
 * [string] class - container class string
 * [string] data - json object string with html-data properties (keys and values)

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
 * (required) Keyword :code:`from` and type of requested Meta's in collection: :code:`from articles` with filters passed to Meta Loader as extra parameters (:code:`start`, :code:`limit`, :code:`order`)
 * (optional) Keyword :code:`with` and parameters for Meta Loader ex.: :code:`with {foo: 'bar', param1: 'value1'}`
 * (optional) Keyword :code:`if` and expression used for results filtering

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


How to work with Meta objects
-----------------------------

On template level every variable in Context and fetched by :code:`gimme` and :code:`gimmelist` is representation of Meta objects.


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

**generate url**

.. code-block:: twig

    {{ url(article) }} // absolute url
    {{ path(article) }} // relative path

example in gimmelist

.. code-block:: twig

    {% gimmelist article from articles %}
        <li><a href="{{ url(article) }}">{{ article.title }} </a></li>
    {% endgimmelist %}
