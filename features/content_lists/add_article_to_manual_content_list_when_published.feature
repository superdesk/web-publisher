@disable-fixtures
@content_lists
Feature: Add article to manual content list when published
  In order to add article to content list when it's published
  As a HTTP Client
  I want to be able to publish article and see it in content lists

  Scenario: Push new article and add it to manual content lists
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default | code   |
      | Default      | test |           | localhost   | true    | true    | 123abc |

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

    Given the following Content Lists:
      | name                  | type      |
      | first content list    | manual    |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |


    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package","version":"2","guid":"16e111d5","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin"
    }
    """

    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/1/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "route":1,
              "is_published_fbia":false,
              "published":true,
              "content_lists":[
                {"id":1,"position":0}
              ]
            }
          ]
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/test-package"
    Then the response status code should be 200
    And the JSON node "status" should be equal to "published"
