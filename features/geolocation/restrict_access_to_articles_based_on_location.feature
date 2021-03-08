@geolocation
@disable-fixtures
Feature: Restricting access to articles based on reader's location
  In order to deny access for readers from different countries
  As a HTTP Client
  I want to be able to exclude some countries from reading the content of the specific articles

  Background:
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      | code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc |

    Given the following Users:
      | username   | email                      | token      | password | role                |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   |

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
    Given the following Routes:
      |  name      | type       | slug |
      |  Test      | collection | test |

    Given default tenant with code "123abc"
    Given the following tenant publishing rule:
    """
      {
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"true == true",
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

  Scenario: Restrict access to the article for the readers from USA
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
     {
      "language":"en",
      "slugline":"lorem",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-05-25T10:23:15+0000",
      "description_text":"some abstract text",
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
      "place":[],
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
    Then the response status code should be 201

    When I go to "/test/lorem"
    Then the response status code should be 200

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
     {
      "language":"en",
      "slugline":"lorem",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-05-25T10:23:15+0000",
      "description_text":"some abstract text",
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
      "place":[
        {
          "country":"Australia",
          "world_region":"Oceania",
          "state":"Australian Capital Territory",
          "qcode":"ACT",
          "name":"ACT",
          "group":"Australia"
        },
        {
          "country":"United States",
          "world_region":"America",
          "state":"Minnesota",
          "qcode":"MN",
          "name":"MN",
          "group":"America"
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
    Then the response status code should be 201

    Then I visit "/test/lorem" page with "20.191.192.0" IP address from "Australia" country
    Then the response status code should be 403

    Then I visit "/test/lorem" page with "128.101.101.101" IP address from "Minnesota" state
    Then the response status code should be 403

    Then I visit "/test/lorem" page with "15.107.141.0" IP address from "Texas" state
    Then the response status code should be 200
