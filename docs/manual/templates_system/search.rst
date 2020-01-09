How to implement Search using ElasticSearch?
============================================

To make use of features (e.g. full-text search) provided by ElasticSearch and it's extension/plugin :doc:`/bundles/SWPElasticSearchBundle/index` created specifically for Superdesk Publisher, you need to do the following steps.

Create a new Route
------------------

You can create a new route using admin interface as described in :doc:`/cookbooks/templates/routes_with_extensions` section. In this example the created route under which we will place search will be named: ``search``.

Create a template file
----------------------

Example search template which loads search and its results when filtered by criteria:

    .. code-block:: twig

        # ../view/search.html.twig
        <form name="filter" method="get">
            <input type="search" id="filter_search" name="q">
        </form>

        {% set itemsPerPage, currentPage = 8, app.request.get('page', 1) %}
        {% set start = ((currentPage - 1) * itemsPerPage) %}

        {% gimmelist article from searchResults|limit(app.request.get('limit', 10))|order(app.request.get('field', 'publishedAt'), app.request.get('direction', 'desc')) with {
            term: app.request.get('q', ''),
            page: app.request.get('page', 1),
            routes: app.request.get('route', []),
            term: app.request.get('q', ''),
            publishedBefore: app.request.get('publishedBefore'),
            publishedAfter: app.request.get('publishedAfter'),
            publishedAt: app.request.get('publishedAt'),
            sources: app.request.get('source', []),
            authors: app.request.get('author', []),
            statuses: app.request.get('status', []),
            metadata: app.request.get('metadata', []),
        } %}
            <h4>{{ article.title }}</h4>
            <p>{{ article.lead }}</p>

        {% if loop.last  %}
            {% include '_tpl/pagination.html.twig' with {
            currentFilters: {}|merge(app.request.query.all()),
            currentPage: currentPage,
            paginationPath: gimme.route,
            lastPage: (loop.totalLength/itemsPerPage)|round(0, 'ceil')
            } only %}

            Showing {{ searchResults|length }} out of {{ loop.totalLength }} articles.
        {% endif %}
        {% endgimmelist %}

        <a href="search?route[]=50">Business</a>
        <a href="search?route[]=49">Politics</a>

Alternatively, to built-in ``order`` function, you can also use ``sort: app.request.get('sort', []),`` parameter to sort by different fields and directions which needs to be passed directly to the ``with`` statement.

Available search criteria:
--------------------------

Based on the above template.

+-----------------+----------------------------+-------------------------------------+------------------------------------+
| Criteria name   | Description                | Example                             | Format                             |
+=================+============================+=====================================+====================================+
| sort            |     Sorting                |  sort[publishedAt]=desc             | sort[<field>]=<direction>          |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| page            |     Pagination             |  page=1                             | page=<page_number>                 |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| limit           |     Items per page         |  limit=10                           | limit=<limit>                      |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| routes          | An array of routes ids     |  route[]=10&route[]=12              | route[]=<routeId>&route[]=<routeId>|
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| q               |     Search query           |  q=Lorem ipsum                      | q=<search_term>                    |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| publishedBefore | Published before date time | publishedBefore=1996-10-15T00:00:00 | publishedBefore=<datetime>         |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| publishedAfter  | Published before date time | publishedBefore=1996-10-15T00:00:00 | publishedAfter=<datetime>          |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| sources         |     Sources of articles    |  source[]=APP&source[]=NTB          |source[]=<source>&source[]=<source> |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| authors         |     An array of authors    |  author[]=Joe&author[]=Doe          |author[]=<auth1>&author[]=<auth2>   |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| statuses        |     An array of statues    |status[]=new&status[]=published      | status[]=new&status[]=published    |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| metadata        |     An array metadata      |metadata[located]=Sydney             | metadata[<field>]=<value>          |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
