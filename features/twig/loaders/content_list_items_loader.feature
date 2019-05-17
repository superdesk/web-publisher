@disable-fixtures
Feature: Working with Content List Items Loader
  In order to work with content list items
  As a template implementator
  I want to be able to get next and previous article from content list

  Scenario: Getting next article from content list
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
      {% gimme article with {slug: "second-test-article"} %}
        {% gimme contentListItem with { contentListName: "test content list", article: article, next: true  } %}
          {{ contentListItem.content.title }}
        {% endgimme %}
      {% endgimme %}
     """
    Then rendered template should contain "Third Test Article"

    And I render a template with content:
     """
      {% gimme article with {slug: "second-test-article"} %}
        {% gimme contentListItem with { contentListName: "test content list", article: article, prev: true  } %}
          {{ contentListItem.content.title }}
        {% endgimme %}
      {% endgimme %}
     """
    Then rendered template should contain "First Test Article"


    And I render a template with content:
     """
      {% gimme article with {slug: "second-test-article"} %}
        {% gimme contentListItem with { contentListName: "test content list", article: article  } %}
          {{ contentListItem.content.title }}
        {% endgimme %}
      {% endgimme %}
     """
    Then rendered template should not contain "First Test Article"

    And I render a template with content:
     """
      {% gimme article with {slug: ""} %}
        aaa
      {% endgimme %}
     """
    Then rendered template should not contain "aaa"
