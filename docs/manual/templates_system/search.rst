How to implement Search using ElasticSearch?
============================================

To make use of features (e.g. full-text search) provided by ElasticSearch and it's extension/plugin :doc:`/bundles/SWPElasticSearchBundle/index` created specifically for Superdesk Publisher, you need to do the following steps.

Create a new Route
------------------

You can create a new route using admin interface as described in :doc:`/cookbooks/templates/routes_with_extensions` section. In this example the created route under which we will place search, will be named: ``search``.

Create a template file
----------------------

Example search template which calls controller and renders search:

    .. code-block:: twig

        # ../view/search.html.twig
        {{ render(controller(
            'SWPElasticSearchBundle:Search:search',
            {
                'template': 'search_results.html.twig',
                'criteria': {
                    'sort': app.request.get('sort'),
                    'page': app.request.get('page', 1),
                    'limit': app.request.get('limit', 10),
                    'routes': app.request.get('route', []),
                    'term': app.request.get('q', ''),
                    'publishedBefore': app.request.get('publishedBefore'),
                    'publishedAfter': app.request.get('publishedAfter'),
                    'publishedAt': app.request.get('publishedAt'),
                    'sources': app.request.get('source', []),
                    'authors': app.request.get('author', []),
                    'statuses': app.request.get('status', []),
                }
            }
        )) }}

and the ``search_results.html.twig`` template:

    .. code-block:: twig

        <form name="filter" method="get">
        <input type="search" id="filter_search" name="q">
        </form>

        # results is meta collection
        {% for article in results %}
            <h4>{{ article.title }}</h4>
            <p>{{ article.lead }}</p>
        {% endfor %}

        {{ dump(criteria) }}
        <a href="search?route[]=50">Business</a>
        <a href="search?route[]=49">Politics</a>

        Showing {{ results|length }} out of {{ total }} articles.

The results variable is of type MetaCollection.

Available search criteria:
--------------------------

Based on the above template.

+-----------------+----------------------------+-------------------------------------+------------------------------------+
| Criteria name   | Description                | Example                             | Format                             |
+=================+============================+=====================================+====================================+
| sort            |     Sorting                |  sort[publishedAt]=desc             | sort[<field>]=<direction>          |
+-----------------+----------------------------+-------------------------------------+------------------------------------+
| page            |     Pagination             |  page=1                             | page=<page_number>                 |
+-----------------+---------------------------+-------------------------------------+------------------------------------+
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
