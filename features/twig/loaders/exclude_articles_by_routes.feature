@disable-fixtures
Feature: Exclude articles by routes
  In order to show a curated list of articles
  As a template implementor
  I want to be able to exclude articles by route ids

  Background:
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default |
      | Default      | test |           | localhost   | true    | true    |

    Given the following Articles:
      | title                | route    | status    |
      | First Test Article   | Sports   | published |
      | Second Test Article  | Sports   | published |
      | Third Test Article   | Politics | published |
      | Health Test Article  | Health   | published |

  Scenario: Exclude articles by routes
    Given I render a template with content:
     """
     {% gimmelist article from articles %}
        {{ article.title }}-{{ article.id }}-{{ article.route.name }}
     {% endgimmelist %}
     """
    Then rendered template should contain "First Test Article-1-Sports"
    Then rendered template should contain "Second Test Article-2-Sports"
    Then rendered template should contain "Third Test Article-3-Politics"
    Then rendered template should contain "Health Test Article-4-Health"

    And I render a template with content:
     """
     {% gimmelist article from articles without { route: [2] } ignoreContext ['route'] %}
        {{ article.title }}-{{ article.id }}-{{ article.route.name }}
     {% endgimmelist %}
     """
    Then rendered template should contain "First Test Article-1-Sports"
    Then rendered template should contain "Second Test Article-2-Sports"
    Then rendered template should not contain "Third Test Article-3-Politics"
    Then rendered template should contain "Health Test Article-4-Health"

    And I render a template with content:
     """
     {% gimmelist article from articles without { route: [1] } ignoreContext ['route'] %}
        {{ article.title }}-{{ article.id }}-{{ article.route.name }}
     {% endgimmelist %}
     """
    Then rendered template should not contain "First Test Article-1-Sports"
    Then rendered template should not contain "Second Test Article-2-Sports"
    Then rendered template should contain "Third Test Article-3-Politics"
    Then rendered template should contain "Health Test Article-4-Health"

    And I render a template with content:
     """
     {% gimmelist article from articles without { route: [2,3] } ignoreContext ['route'] %}
        {{ article.title }}-{{ article.id }}-{{ article.route.name }}
     {% endgimmelist %}
     """
    Then rendered template should contain "First Test Article-1-Sports"
    Then rendered template should contain "Second Test Article-2-Sports"
    Then rendered template should not contain "Third Test Article-3-Politics"
    Then rendered template should not contain "Health Test Article-4-Health"
