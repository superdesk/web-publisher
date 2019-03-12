@disable-fixtures
Feature: Working with Content List Items Loader
  In order to work with content list items
  As a template implementator
  I want to be able to get next and previous article from content list

  Scenario: Getting next article from content list
    Given the following Content Lists:
      | name              | type   |
      | test content list | manual |

    Given the following Articles:
      | title               | route      | status    |
      | Test Article        | Test Route | published |
      | Second Test Article | Test Route | published |
      | Third Test Article  | Test Route | published |

    Given the following Content List Items:
      | content list      | article |
      | test content list | Test Article |
      | test content list | Second Test Article |
      | test content list | Third Test Article |

    And I render a template with content:
     """
      {% gimme article from articles with {slug: "second-test-article" %}
        {% gimmelist item from contentListItems with { contentListName: "test content list", article: article, next: true  } %}
          {{ item.content.title }}
        {% endgimmelist %}
      {% endgimme %}
     """
    Then rendered template should contain "Third Test Article"