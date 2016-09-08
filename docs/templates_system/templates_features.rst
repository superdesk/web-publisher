.. _templates_features:

Templates features
==================

Gimme and SWP custom Twig tags
------------------------------

Gimme allows you to fetch the Meta object you need in any place of your template. It supports single Meta objects (with :code:`gimme` ) and collections of Meta objects (with :code:`gimmelist`).

container
`````````

The :code:`container` tag has one required parameter and one optional parameter:

 * (required) container unique name, for example: *frontpage_sidebar*
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

Meta Loaders sometimes require special parameters - like the article number, language of the article, user id, etc..

.. code-block:: twig

    {% gimme article with { articleNumber: 1 } %}
        {# Meta Loader will use provided parameters to load article Meta #}
        {{ article.title }}
    {% endgimme %}

gimmelist
`````````

The :code:`gimmelist` tag has two required parameters and two optional parameters:

 * (required) Name of variable available inside block: :code:`article`
 * (required) Keyword :code:`from` and type of requested Metas in collection: :code:`from articles` with filters passed to Meta Loader as extra parameters (:code:`start`, :code:`limit`, :code:`order`)
 * (optional) Keyword :code:`with` and parameters for Meta Loader, for example: :code:`with {foo: 'bar', param1: 'value1'}`
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


How to work with :code:`gimmelist` pagination?
----------------------------------------------

:code:`gimmelist` is based on Twig :code:`for` tag, like in Twig there is `loop <http://twig.sensiolabs.org/doc/tags/for.html#the-loop-variable>`_ variable available.
In addition to default loop properties there is also :code:`totalLength`. It's filled by loader with number of total elements in storage which are matching criteria. Thanks to this addition we can build real pagination.

:code:`TemplateEngine` Bundle provides simple default pagination template file: :code:`pagination.html.twig`.

.. note::

    You can override that template with :code:`SWPTemplateEngineBundle/views/pagination.html.twig` file in Your theme. Or You can use own file used for pagination rendering.

Here is commented example of pagination:

.. code-block:: twig

    {# Setup list and pagination parameters #}
    {% set itemsPerPage, currentPage = 1, app.request.get('page', 1) %}
    {% set start = (currentPage / itemsPerPage) - 1 %}

    {# List all articles from route '/news' and limit them to `itemsPerPage` value starting from `start` value #}
    {% gimmelist article from articles|start(start)|limit(itemsPerPage) with {'route': '/news'} %}
        <li><a href="{{ url(article) }}">{{ article.title }} </a></li>

        {# Render pagination only at end of list #}
        {% if loop.last  %}
            {#
                Use provided by default pagination template

                Parameters:
                * currentFilters (array) : associative array that contains the current route-arguments
                * currentPage (int) : the current page you are in
                * paginationPath (Meta|string) : the route name (or supported by router Meta object) to use for links
                * lastPage (int) : represents the total number of existing pages
                * showAlwaysFirstAndLast (bool) : Always show first and last link (just disabled)
            #}
            {% include '@SWPTemplateEngine/pagination.html.twig' with {
                currentFilters: {}|merge(app.request.query.all()),
                currentPage: currentPage,
                paginationPath: gimme.route,
                lastPage: (loop.totalLength/itemsPerPage)|round(1, 'ceil'),
                showAlwaysFirstAndLast: true
            } only %}
        {% endif %}
    {% endgimmelist %}



How to work with Meta objects
-----------------------------

On the template level, every variable in Context and fetched by :code:`gimme` and :code:`gimmelist` is a representation of Meta objects.


**dump**

.. code-block:: twig

    {{ dump(article) }}

**print**

.. code-block:: twig

    {{ article }}

If the meta configuration has the :code:`to_string` property then the value of this property will be printed, otherwise it will be represented as JSON.

**access property**

.. code-block:: twig

    {{ article.title }}
    {{ article['title']}}

**generate url**

.. code-block:: twig

    {{ url(article) }}    // absolute url
    {{ path(article) }}   // relative path

Here's an example using gimmelist:

.. code-block:: twig

    {% gimmelist article from articles %}
        <li><a href="{{ url(article) }}">{{ article.title }} </a></li>
    {% endgimmelist %}


Stringy twig extensions
-----------------------

We have extended the twig syntax, adding a number of functions for working with strings from a php library. A list of the functions together with a description of each, and of how they are to be invoked in PHP can be found here: https://github.com/danielstjules/Stringy#instance-methods

To call one of these functions in twig, if it returns a boolean, it is available as a twig function. So, for example, the function contains() can be called like this in twig:

.. code-block:: twig

    {% set string_var = 'contains' %}
    {% if contains(string_var, 'tain') %}string_var{% endif %} // will render contains

Any php function which returns a string is available in twig as a filter. So, for example, the function between() can be called like this in twig:

.. code-block:: twig

    {% set string_var = 'Beginning' %}
    {{ string_var|between('Be', 'ning') }} // will render gin

And the function camelize(), which doesn't require any parameters, can simply be called like this:

.. code-block:: twig

    {% set string_var = 'Beginning' %}
    {{ string_var|camelize }} // will render bEGINNING
