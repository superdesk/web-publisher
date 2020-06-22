@disable-fixtures
@content_lists
Feature: Add article to automated content lists
  In order to add article to automatic content lists based on metadata subjects (categories) and services
  As a HTTP Client
  I want to be able to push new content and see it in content lists

  Scenario: Test if articles are added to the proper content lists based on filters
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default | code   |
      | Default      | test |           | localhost   | true    | true    | 123abc |

    Given the following Content Lists:
      | name                  | type      | filters                           |
      | 1 content list   | automatic | {"metadata":{"subject":[{"code": "lent2020", "scheme": "customVocabulary"}]}} |
      | 2 content list   | automatic | {"metadata":{"service":[{"code": "vatican"}],"subject":[{"code": "news", "scheme": "atype"}]}} |
      | 3 content list   | automatic | {"metadata":{"service":[{"code": "vatican"}, {"code": "something"}]}} |

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
       "language":"en",
       "headline":"Test Package Vatican 2",
       "version":"2",
       "guid":"16e111d572",
       "priority":6,
       "type":"text",
       "service":[
          {
             "name":"Vatican",
             "code":"vatican"
          }
       ],
       "authors":[
          {
             "name":"Tom Doe",
             "role":"editor"
          }
       ],
       "byline":"Admin",
       "subject":[
          {
             "name":"News",
             "scheme":"atype",
             "code":"news"
          },
          {
             "code":"001",
             "scheme":"test2",
             "name":"priest"
          }
       ]
    }
    """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/3/items/"
    And the JSON node "total" should be equal to "1"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/2/items/"
    And the JSON node "total" should be equal to "1"

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
       "language":"en",
       "headline":"Test Package",
       "version":"2",
       "guid":"16e111d5",
       "priority":6,
       "type":"text",
       "authors":[
          {
             "name":"Tom Doe",
             "role":"editor"
          }
       ],
       "byline":"Admin",
       "subject":[
          {
             "name":"Lent 2020",
             "scheme":"customVocabulary",
             "code":"lent2020"
          },
          {
             "code":"001",
             "scheme":"test2",
             "name":"priest"
          }
       ]
    }
    """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/"
    And the JSON node "total" should be equal to "1"

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
       "language":"en",
       "headline":"Test Package Nation with subjects",
       "version":"2",
       "guid":"16e111d578",
       "priority":6,
       "type":"text",
       "service":[
          {
             "name":"Nation",
             "code":"nation"
          }
       ],
       "authors":[
          {
             "name":"Tom Doe",
             "role":"editor"
          }
       ],
       "byline":"Admin",
       "subject":[
          {
             "name":"News",
             "scheme":"atype",
             "code":"news"
          },
          {
             "code":"001",
             "scheme":"test2",
             "name":"priest"
          }
       ]
    }
    """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/2/items/"
    And the JSON node "total" should be equal to "2"

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
       "language":"en",
       "headline":"Test Package Vatican",
       "version":"2",
       "guid":"16e111d57855",
       "priority":6,
       "type":"text",
       "service":[
          {
             "name":"Vatican",
             "code":"vatican"
          }
       ],
       "authors":[
          {
             "name":"Tom Doe",
             "role":"editor"
          }
       ],
       "byline":"Admin",
       "subject":[
          {
             "name":"News",
             "scheme":"atype",
             "code":"news"
          },
          {
             "code":"001",
             "scheme":"test2",
             "name":"priest"
          }
       ]
    }
    """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/2/items/"
    And the JSON node "total" should be equal to "3"


    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
       "language":"en",
       "headline":"Test Package Nation without subjects",
       "version":"2",
       "guid":"22216e111d578",
       "priority":6,
       "type":"text",
       "service":[
          {
             "name":"Nation",
             "code":"nation"
          }
       ],
       "authors":[
          {
             "name":"Tom Doe",
             "role":"editor"
          }
       ],
       "byline":"Admin",
       "subject":[
       ]
    }
    """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/2" with body:
    """
    {
        "filters": "{\"metadata\":{\"service\":[{\"code\": \"nation\"}],\"subject\":[{\"code\": \"news\", \"scheme\": \"atype\"}]}}"
    }
    """
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/2/items/"
    And the JSON node "total" should be equal to "4"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/content/lists/2" with body:
    """
    {
        "filters": "{\"metadata\":{\"service\":[{\"code\": \"nation\"}]}}"
    }
    """
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/2/items/"
    And the JSON node "total" should be equal to "2"
