@disable-fixtures
@content_lists
Feature: Modify limit on manual content lists
  In order to test if limit on content list is correctly changes
  As a HTTP Client
  I want to be able to see correct list state after it's limit change

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
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
            "tenant":"123abc",
            "route":1,
            "is_published_fbia":false,
            "published":true,
            "contentLists": [
              {"id": 1, "position": 0}
            ],
            "packageGuid": "16e111d1"
      }
    """
    Then the response status code should be 200

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package 1","version":"2","guid":"16e111d1","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin"
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
            "tenant":"123abc",
            "route":1,
            "is_published_fbia":false,
            "published":true,
            "contentLists": [
              {"id": 1, "position": 0}
            ],
            "packageGuid": "16e111d2"
      }
    """
    Then the response status code should be 200

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package 2","version":"2","guid":"16e111d2","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin"
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
            "tenant":"123abc",
            "route":1,
            "is_published_fbia":false,
            "published":true,
            "contentLists": [
              {"id": 1, "position": 0}
            ],
            "packageGuid": "16e111d3"
      }
    """
    Then the response status code should be 200
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package 3","version":"2","guid":"16e111d3","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin"
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
            "tenant":"123abc",
            "route":1,
            "is_published_fbia":false,
            "published":true,
            "contentLists": [
              {"id": 1, "position": 0}
            ],
            "packageGuid": "16e111d4"
      }
    """
    Then the response status code should be 200
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package 4","version":"2","guid":"16e111d4","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin"
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
            "tenant":"123abc",
            "route":1,
            "is_published_fbia":false,
            "published":true,
            "contentLists": [
              {"id": 1, "position": 0}
            ],
            "packageGuid": "16e111d5"
      }
    """
    Then the response status code should be 200
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package 5","version":"2","guid":"16e111d5","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin"
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
            "tenant":"123abc",
            "route":1,
            "is_published_fbia":false,
            "published":true,
            "contentLists": [
              {"id": 1, "position": 0}
            ],
            "packageGuid": "16e111d6"
      }
    """
    Then the response status code should be 200
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package 6","version":"2","guid":"16e111d6","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/"
    And the JSON node "_embedded._items[0].content.id" should be equal to "6"
    And the JSON node "_embedded._items[1].content.id" should be equal to "5"
    And the JSON node "_embedded._items[2].content.id" should be equal to "4"
    And the JSON node "_embedded._items[3].content.id" should be equal to "3"
    And the JSON node "_embedded._items[4].content.id" should be equal to "2"
    And the JSON node "_embedded._items[5].content.id" should be equal to "1"
    And the JSON node "total" should be equal to 6


    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1" with body:
    """
    {
        "limit": 5
    }
    """
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/"
    And the JSON node "total" should be equal to 5
    And the JSON node "_embedded._items[0].content.id" should be equal to "6"
    And the JSON node "_embedded._items[1].content.id" should be equal to "5"
    And the JSON node "_embedded._items[2].content.id" should be equal to "4"
    And the JSON node "_embedded._items[3].content.id" should be equal to "3"
    And the JSON node "_embedded._items[4].content.id" should be equal to "2"
