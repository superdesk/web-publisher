.. _templates:

Templates
=========

Gimme and SWP custom Twig tags
------------------------------

Gimme allows you to fetch the Meta object you need in any place of your template. It supports single Meta objects (with :code:`gimme` ) and collections of Meta objects (with :code:`gimmelist`).

container
`````````

The :code:`container` tag has one required parameter and one optional parameter:

 * (required) container unique name ex.: *frontpage_sidebar*
 * (optional) keyword :code:`with` and default parameters for containers (used to create the container on theme installation).

Here is an example of a container tag:

.. code-block:: twig

     {% container 'frontpage_sidebar' with {
         'width': 400,
         'height': 500,
         'styles': 'border: solid 1px red',
         'class': 'css_class_name',
         'data': {'custom-key': value}
     }%}
     {% endcontainer %}

This container tag will render the HTML code:

.. code-block:: html

    <div id="frontpage_sidebar" class="swp_container css_class_name" style="width: 400px; height: 500px; border: solid 1px red;" data-custom-key="value"></div>

The available container parameters are:

 * [integer] width - container width
 * [integer] height - container height
 * [string] styles - container inline styles
 * [string] class - container class string
 * [string] data - JSON object string with html-data properties (keys and values)

gimme
`````

The tag :code:`gimme` has one required parameter and one optional parameter:

 * (required) Meta object type (and name of variable available inside block), for example: *article*
 * (optional) Keword :code:`with` and parameters for Meta Loader, for example: :code:`{ param: "value" }`

.. code-block:: twig

    {% gimme article %}
        {# article Meta will be available under "article" variable inside block #}
        {{ article.title }}
    {% endgimme %}

Meta Loaders sometimes requires special parameters - like the article number, language of the article, user id, etc..

.. code-block:: twig

    {% gimme article with { articleNumber: 1 } %}
        {# Meta Loader will use provided parameters to load article Meta #}
        {{ article.title }}
    {% endgimme %}

gimmelist
`````````

Tag :code:`gimmelist` have two required parameters and two optional parameters:

 * (required) Name of variable available inside block: :code:`article`
 * (required) Keyword :code:`from` and type of requested Metas in collection: :code:`from articles` with filters passed to Meta Loader as extra parameters (:code:`start`, :code:`limit`, :code:`order`)
 * (optional) Keyword :code:`with` and parameters for Meta Loader ex.: :code:`with {foo: 'bar', param1: 'value1'}`
 * (optional) Keyword :code:`if` and expression used for results filtering

Here is an example of the required parameters:

.. code-block:: twig

    {% gimmelist article from articles %}
        {{ article.title }}
    {% endgimmelist %}

An here's an example using all parameters:

.. code-block:: twig

    {% gimmelist article from articles|start(0)|limit(10)|order('id', 'desc')
        with {foo: 'bar', param1: 'value1'}
        if article.title == "New Article 1"
    %}
        {{ article.title }}
    {% endgimmelist %}


How to work with Meta objects
-----------------------------

On the template level, every variable in Context and fetched by :code:`gimme` and :code:`gimmelist` is a representation of Meta objects.


**dump**

.. code-block:: twig

    {{ dump(article) }}

**print**

.. code-block:: twig

    {{ article }} - if the meta configuration has the to_string property then the value of this property will be printed, otherwise it will be represented as JSON.

**access property**

.. code-block:: twig

    {{ article.title }}
    {{ article['title']}}

**generate url**

.. code-block:: twig

    {{ url(article) }} // absolute url
    {{ path(article) }} // relative path

Here's an example using gimmelist:

.. code-block:: twig

    {% gimmelist article from articles %}
        <li><a href="{{ url(article) }}">{{ article.title }} </a></li>
    {% endgimmelist %}
