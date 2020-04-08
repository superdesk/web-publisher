@disable-fixtures
Feature: Working with Content List limit
  In order to always have set number of items in content list (no more than set limit)
  As a api user
  I want to be able to add new list items and publisher should remove last one above the limit

  Scenario: Adding items above the limit
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default | code   |
      | Default      | test |           | localhost   | true    | true    | 123abc |

    Given the following Content Lists:
      | name                | type   | limit |
      | test content list   | manual | 3     |

    Given the following Articles:
      | title               | route      | status    |
      | First Test Article  | Test Route | published |
      | Second Test Article | Test Route | published |
      | Third Test Article  | Test Route | published |
      | Forth Test Article  | Test Route | published |
      | Fifth Test Article  | Test Route | published |

    Given the following Content List Items:
      | content_list      | article             |
      | test content list | First Test Article  |
      | test content list | Second Test Article |
      | test content list | Third Test Article  |

    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

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
    Then I send a "GET" request to "/api/v2/content/lists/1/items/"
    And the JSON node "total" should be equal to 3
