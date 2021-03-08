@disable-fixtures
Feature: Working with article redirects and orignal urls
  In order to always have access to first published article url
  As a template implementator
  I want to be able to get that first published url in template

  Scenario: Working with saved cache tags with twig cache used
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following Users:
      | username   | email                      | token      | password | role                |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   |

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/" with body:
    """
    {
        "name":"override_slug_on_correction",
        "value":true
    }
    """

    Given the following organization publishing rule:
    """
      {
        "name":"Test rule",
        "description":"Test rule description",
        "priority":1,
        "expression":"true == true",
        "configuration":[
          {
            "key":"destinations",
            "value":[
              {
                "tenant":"123abc"
              }
            ]
          }
        ]
      }
    """

    Given default tenant with code "123abc"
    Given the following tenant publishing rule:
    """
      {
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"article.getPackage().getLanguage() == 'en'",
          "configuration":[
            {
              "key":"route",
              "value":1
            },
            {
              "key":"published",
              "value":true
            }
          ]
       }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package","version":"2","guid":"16e111d5","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin",
        "subject":[{"code":"02002001","scheme":"test","name":"lawyer"}, {"code":"001","scheme":"test2","name":"priest"}]
    }
    """

    And I render a template with content:
     """
        {% gimme article with {slug: "test-package"} %}<a href="{{ original_url(article) }}">{{ article.title }}</a>{% endgimme %}
     """
    Then rendered template should contain '<a href="http://localhost/test/test-package">Test Package</a>'

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package renamed","version":"2","guid":"16e111d5","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin",
        "subject":[{"code":"02002001","scheme":"test","name":"lawyer"}, {"code":"001","scheme":"test2","name":"priest"}]
    }
    """

    And I render a template with content:
     """
        {% gimme article with {slug: "test-package-renamed"} %}<a href="{{ original_url(article) }}">{{ article.title }}</a>{% endgimme %}
     """
    Then rendered template should contain '<a href="http://localhost/test/test-package">Test Package renamed</a>'

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/test-package"
    Then the response status code should be 404

    And I render a template with content:
     """
        {% gimme article with {slug: "test-package-renamed"} %}<a href="{{ url(article) }}">{{ article.title }}</a>{% endgimme %}
     """
    Then rendered template should contain '<a href="http://localhost/test/test-package-renamed">Test Package renamed</a>'
