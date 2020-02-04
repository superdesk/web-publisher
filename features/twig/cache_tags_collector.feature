@disable-fixtures
Feature: Working with cache blocks http cache tags collector
  In order to always have correct http cache tags with used on page articles
  As a template implementator
  I want to be able to have http response tags set when content is cached and not cached

  Scenario: Working with saved cache tags with twig cache used
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default |
      | Default      | test |           | localhost   | true    | true    |

    Given the following Content Lists:
      | name                | type   |
      | test content list   | manual |

    Given the following Articles:
      | title               | route      | status    |
      | First Test Article  | Test Route | published |
      | Second Test Article | Test Route | published |
      | Third Test Article  | Test Route | published |

    Given the following Content List Items:
      | content_list      | article             |
      | test content list | First Test Article  |
      | test content list | Second Test Article |
      | test content list | Third Test Article  |

    And I render a template with content:
     """
      {% cache 'v1' {time: 10} %}
        {% gimmelist article from articles %}
            {{ article.title }}-{{ article.id }}
        {% endgimmelist %}
      {% endcache %}
     """
    Then rendered template should contain "First Test Article-1"
    Then rendered template should contain "Second Test Article-2"
    Then rendered template should contain "Third Test Article-3"
    Then CacheBlockTagsCollector should have tag "a-1"
    Then CacheBlockTagsCollector should have tag "a-2"
    Then CacheBlockTagsCollector should have tag "a-3"

    And I render a template with content:
     """
      {% cache 'v1' {time: 10} %}
        {% gimmelist article from articles %}
            // remove id's but it should still pass as cached version should be used
            {{ article.title }}
        {% endgimmelist %}
      {% endcache %}
     """
    Then rendered template should contain "First Test Article-1"
    Then rendered template should contain "Second Test Article-2"
    Then rendered template should contain "Third Test Article-3"
    Then CacheBlockTagsCollector should have tag "a-1"
    Then CacheBlockTagsCollector should have tag "a-2"
    Then CacheBlockTagsCollector should have tag "a-3"
