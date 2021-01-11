@organization_commands
@disable-fixtures
@custom-env
Feature: Reprocessing already send to publisher packages
  In order to apply new rules or content modifications to already pushed to publisher packages
  As a console command
  I want to be able to pick and reprocess packages

  Scenario: Reprocessing packages with commands:
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

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

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

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

    Given the following Package ninjs:
    """
      {
          "language":"en","headline":"Test Package","version":"2","guid":"16e111d5","priority":6,"type":"text",
          "authors":[{"name":"Tom Doe","role":"editor"}],
          "extra":{
            "articleNumber":"1"
          }
      }
    """

    Given default tenant with code "123abc"
    Given the following Package ninjs:
    """
      {
          "language":"en","headline":"Test Package","version":"2","guid":"fc0a805e","priority":6,"type":"text",
          "authors":[{"name":"John Doe","role":"editor"}],
          "extra":{
            "articleNumber":"1"
          }
      }
    """

    Given default tenant with code "123abc"
    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Then I send a "GET" request to "/redirecting/extra/articleNumber/1"
    Then the response status code should be 301
    And the header "location" should contain "http://localhost/test/test-package"

    Then I send a "GET" request to "http://localhost/test/test-package"
    Then the response status code should be 200

    Then I send a "GET" request to "http://localhost/test/test-package-wrong"
    Then the response status code should be 301
    And the header "location" should contain "http://localhost/test"

    When I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/articles/1" with body:
     """
      {
          "status": "unpublished"
      }
     """
    Then the response status code should be 200

    Then I send a "GET" request to "/redirecting/extra/articleNumber/1"
    Then the response status code should be 301
    And the header "location" should contain "http://localhost/test/test-package-0123456789abc"
