@articles
@disable-fixtures
Feature: Article attach to content route on publish

  Scenario: Check if an article has been attached to content route successfully
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default | themeName      | code   |
      | Default      | test |           | localhost  | true    | true    | swp/test-theme | 123abc |

    Given the following Routes:
      | name | type    | slug |
      | test | content | test |

    Given the following Users:
      | username  | email                      | token      | password | role              | enabled |
      | test.user | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API | true    |

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
      "language":"en",
      "slugline":"lorem",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-05-25T10:23:15+0000",
      "description_text":"some abstract text",
      "profile":"Article",
      "place":[
        {
          "country":"Australia",
          "world_region":"Oceania",
          "state":"Australian Capital Territory",
          "qcode":"ACT",
          "name":"ACT",
          "group":"Australia"
        }
      ],
      "version":"2",
      "byline":"ADmin",
      "keywords":[
        "keyword1",
        "keyword2"
      ],
      "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0",
      "priority":6,
      "subject":[
        {
          "name":"lawyer",
          "code":"02002001"
        }
      ],
      "urgency":3,
      "type":"text",
      "headline":"Lorem",
      "service":[
        {
          "name":"Australian General News",
          "code":"a"
        }
      ],
      "description_html":"<p><b><u>some abstract text</u></b></p>",
      "located":"Warsaw",
      "pubstatus":"usable"
    }
    """

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/routes/1"
    Then the response status code should be 200
    And the JSON node "content.title" should contain "Lorem"

  Scenario: Check if an article has been detached from content route successfully
    When I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/articles/lorem" with body:
     """
      {
          "status": "unpublished"
      }
     """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/lorem"
    Then the response status code should be 200
    And the JSON node "slug" should be equal to "lorem"
    And the JSON node "status" should be equal to "unpublished"
    Then I am authenticated as "test.user"
    And I send a "GET" request to "/api/v2/content/routes/1"
    Then the response status code should be 200
    And the JSON node "content" should be null
