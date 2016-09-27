Custom Twig tags
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
         'cssClass': 'css_class_name',
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
 * [string] cssClass - container class string
 * [string] data - JSON object string with html-data properties (keys and values)

.. include:: /components/TemplatesSystem/features/customTags/gimme.rst
.. include:: /components/TemplatesSystem/features/customTags/gimmelist.rst


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