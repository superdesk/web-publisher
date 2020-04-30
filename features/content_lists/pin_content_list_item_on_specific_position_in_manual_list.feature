@disable-fixtures
@content_lists
Feature: Pin/unpin article in manual content list on any position
  In order to pin article on specific position in the manual content list
  As a HTTP Client
  I want to be able to set the position of the pinned article

  Scenario: Pin articles on specific position in the manual content list
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default | code   |
      | Default      | test |           | localhost   | true    | true    | 123abc |

    Given the following Content Lists:
      | name              | type      |
      | my content list   | manual    |

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
        "language":"en","headline":"Test Package1","version":"2","guid":"16e111d5","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin",
        "subject":[{"name":"lawyer","code":"02002001"}]
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package2","version":"2","guid":"16e111d6","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin",
        "subject":[{"name":"lawyer","code":"02002001"}]
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package3","version":"2","guid":"16e111d7","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin",
        "subject":[{"name":"lawyer","code":"02002001"}]
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package4","version":"2","guid":"16e111d8","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin",
        "subject":[{"name":"lawyer","code":"02002001"}]
    }
    """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 0

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":1,
             "action":"add",
             "position":0,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T13:51:58+00:00"
    }
    """
    Then the response status code should be 201

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":2,
             "action":"add",
             "position":0,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T13:52:58+00:00"
    }
    """
    Then the response status code should be 201

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":3,
             "action":"add",
             "position":0,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T13:53:58+00:00"
    }
    """
    Then the response status code should be 201

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":4,
             "action":"add",
             "position":0,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T13:54:58+00:00"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 4

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":4,
             "action":"move",
             "position":1,
             "sticky": true
          }
       ],
       "updated_at":"2050-04-29T13:55:58+00:00"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 4
    And the JSON node "_embedded._items[1].content.title" should be equal to "Test Package4"
    And the JSON node "_embedded._items[1].position" should be equal to 1
    And the JSON node "_embedded._items[1].sticky_position" should be equal to 1

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":3,
             "action":"move",
             "position":1,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T13:56:58+00:00"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 4

    And the JSON node "_embedded._items[0].content.title" should be equal to "Test Package3"
    And the JSON node "_embedded._items[0].position" should be equal to 0
    And the JSON node "_embedded._items[0].sticky_position" should be equal to 0

    And the JSON node "_embedded._items[1].content.title" should be equal to "Test Package4"
    And the JSON node "_embedded._items[1].position" should be equal to 1
    And the JSON node "_embedded._items[1].sticky_position" should be equal to 1

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":4,
             "action":"move",
             "position":1,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T13:57:58+00:00"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 4

    And the JSON node "_embedded._items[1].content.title" should be equal to "Test Package4"
    And the JSON node "_embedded._items[1].position" should be equal to 1
    And the JSON node "_embedded._items[1].sticky" should be false

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":3,
             "action":"move",
             "position":1,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T13:59:58+00:00"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 4

    And the JSON node "_embedded._items[0].content.title" should be equal to "Test Package4"
    And the JSON node "_embedded._items[0].position" should be equal to 0
    And the JSON node "_embedded._items[0].sticky" should be false

    And the JSON node "_embedded._items[1].content.title" should be equal to "Test Package3"
    And the JSON node "_embedded._items[1].position" should be equal to 1
    And the JSON node "_embedded._items[1].sticky_position" should be null


    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"Test Package5","version":"2","guid":"16e111d9","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"Admin",
        "subject":[{"name":"lawyer","code":"02002001"}]
    }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":5,
             "action":"add",
             "position":1,
             "sticky": true
          }
       ],
       "updated_at":"2050-04-29T14:05:58+00:00"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 5

    And the JSON node "_embedded._items[0].content.title" should be equal to "Test Package4"
    And the JSON node "_embedded._items[0].position" should be equal to 0
    And the JSON node "_embedded._items[0].sticky" should be false

    And the JSON node "_embedded._items[1].content.title" should be equal to "Test Package5"
    And the JSON node "_embedded._items[1].position" should be equal to 1
    And the JSON node "_embedded._items[1].sticky" should be true
    And the JSON node "_embedded._items[1].sticky_position" should be equal to 1


    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":1,
             "action":"move",
             "position":1,
             "sticky": false
          }
       ],
       "updated_at":"2050-04-29T14:06:58+00:00"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/?sorting[position]=asc"
    And the JSON node "total" should be equal to 5

    And the JSON node "_embedded._items[0].content.title" should be equal to "Test Package4"
    And the JSON node "_embedded._items[0].position" should be equal to 0
    And the JSON node "_embedded._items[0].sticky" should be false

    And the JSON node "_embedded._items[1].content.title" should be equal to "Test Package5"
    And the JSON node "_embedded._items[1].position" should be equal to 1
    And the JSON node "_embedded._items[1].sticky" should be true
    And the JSON node "_embedded._items[1].sticky_position" should be equal to 1

    And the JSON node "_embedded._items[2].content.title" should be equal to "Test Package1"
    And the JSON node "_embedded._items[2].position" should be equal to 2
    And the JSON node "_embedded._items[2].sticky" should be false

    # stick item on the position where there is already a stick item
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/1/items/" with body:
    """
    {
       "items":[
          {
             "content_id":1,
             "action":"move",
             "position":1,
             "sticky": true
          }
       ],
       "updated_at":"2050-04-29T14:07:58+00:00"
    }
    """
    Then the response status code should be 409
