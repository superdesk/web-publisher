@disable-fixtures
Feature: Working with Search Results Loader
  In order to work with search results articles
  As a template implementator
  I want to be able to get articles from search results

  Scenario: Getting article from search result
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default |
      | Default      | test |           | localhost   | true    | true    |

    Given the following Articles:
      | title               | route      | status    |
      | First Test Article  | Test Route | published |
      | Second Test Article | Test Route | published |
      | Third Test Article  | Test Route | published |

    And I render a template with content:
     """
        {% gimmelist article from searchResults|limit(app.request.get('limit', 100))
        |order(app.request.get('field', 'publishedAt'), app.request.get('direction', 'desc')) with {
        page: app.request.get('page', 1),
        routes: app.request.get('route', []),
        term: 'Article',
        publishedBefore: app.request.get('publishedBefore'),
        publishedAfter: app.request.get('publishedAfter'),
        publishedAt: app.request.get('publishedAt'),
        sources: app.request.get('source', []),
        authors: app.request.get('author', []),
        statuses: app.request.get('status', []),
        metadata: app.request.get('metadata', []),
        keywords: app.request.get('keywords', []),
        } %}
        <h4>{{ article.title }}</h4>
        <p>{{ article.lead }}</p>
    {% endgimmelist %}
     """
    Then rendered template should contain "Test Article"
