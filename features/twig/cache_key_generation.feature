@disable-fixtures
Feature: Working with twig cache blocks
  In order to cache parts of the template
  As a template implementator
  I want to be able to use cached html instead original template code when it's cached

  Scenario: Working with cache blocks
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
      {% gimme route with {name: 'Test Route'} %}
        {% cache 'v1'~route.name { gen: route } %}
          {% gimmelist article from articles %}
              {{ article.title }}-{{ article.id }}
          {% endgimmelist %}
        {% endcache %}
        {% cache 'v1'~route.name { gen: route } %}
          {% gimmelist article from articles %}
              {{ article.title }}-{{ article.id }}
          {% endgimmelist %}
        {% endcache %}
      {% endgimme %}
     """
    Then rendered template should contain "First Test Article-1"
    Then rendered template should contain "Second Test Article-2"
    Then rendered template should contain "Third Test Article-3"
