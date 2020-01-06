@disable-fixtures
@content_lists
Feature: Add article to automated content lists
  In order to add article to automatic content lists based on metadata subjects (categories)
  As a HTTP Client
  I want to be able to push new content and see it in content lists

  Scenario: Test if filters and article metadata/extra are ordered alphabetically before comparision
    Given the following Tenants:
    | organization | name | subdomain | domain_name | enabled | default | code   |
    | Default      | test |           | localhost   | true    | true    | 123abc |

    Given the following Content Lists:
    | name                  | type      | filters                           |
    | first content list    | automatic | {"metadata":{}, "route": [1]}     |
    | second content list   | automatic | {"route":[1]}                     |
    | third content list   | automatic | {"metadata":{"subject":[{"name":"lawyer","scheme":"test","code":"02002001"}]}} |


    Given the following Users:
    | username   | email                      | token      | plainPassword | role                | enabled |
    | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

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
            "subject":[{"code":"02002001","scheme":"test","name":"lawyer"}]
        }
        """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/3/items/"
    And the JSON node "total" should be equal to "1"
