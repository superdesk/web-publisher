@disable-fixtures
Feature: Exclude articles by author
  In order to show a curated list of articles
  As a template implementor
  I want to be able to exclude articles by author names

  Background:
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default |
      | Default      | test |           | localhost   | true    | true    |

    Given the following Articles:
      | title                | route    | status    | authors     |
      | First Test Article   | Sports   | published | Admin       |
      | Second Test Article  | Sports   | published | Admin       |
      | Third Test Article   | Politics | published | Admin       |
      | Sport Test Article   | Sports   | published | Admin,Promo |
      | Promo Test Article   | Sports   | published | Promo       |
      | Promo2  Test Article | Sports   | published | Promo       |
      | Health Test Article  | Health   | published | Admin       |
      | Fitness Test Article | Health   | published | Admin       |

  Scenario: Exclude articles by routes
    Given I render a template with content:
      """
      {% gimmelist
         article from articles|start(0)|limit(5)|order('id','asc')
         without {'author': "Promo"}
         ignoreContext
      %}
        {{ article.title }}
      {% endgimmelist %}
      """
    Then rendered template should be equal to:
    """
      First Test Article
      Second Test Article
      Third Test Article
      Health Test Article
      Fitness Test Article

    """
