@disable-fixtures
@content_lists
Feature: Fill automated list with latest articles when criteria are empty
  In order to add all articles (with content list limit) to content list
  As a HTTP Client
  I want to be able to create new automatic content list and have it filled with articles

  Scenario: Test if list will be filled with articles when criteria are empty
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default | code   |
      | Default      | test |           | localhost   | true    | true    | 123abc |

    Given the following Content Lists:
      | name                  | type      | filters  |
      | first content list    | automatic | {}       |


    Given the following Users:
      | username   | email                      | token      | password | role                |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   |

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

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

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/"
    And the JSON node "total" should be equal to "0"

    Given the following Content Lists:
      | name                   | type      | filters  |
      | second content list    | automatic | {}       |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/2" with body:
    """
    {
        "filters": null
    }
    """
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/2/items/"
    And the JSON node "total" should be equal to "1"
