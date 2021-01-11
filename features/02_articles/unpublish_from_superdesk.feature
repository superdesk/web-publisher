@articles
@disable-fixtures
Feature: As a user I want to be able to unpublish from superdesk previously published package
  In order to have the possibility to publish un-published package
  As a HTTP Client
  I want to be able to re-publish already un-published package

  Scenario: Unpublish article
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      |  code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc  |

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given the following Package ninjs:
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

    And I publish the submitted package "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0":
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true,
              "route": 1
            }
          ]
      }
     """

    When I go to "/test/lorem"
    Then the response status code should be 200

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/lorem"
    Then the response status code should be 200
    And the JSON node "status" should be equal to "published"

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
      "pubstatus":"unpublished"
    }
    """
    Then the response status code should be 201

    When I go to "/test/lorem"
    Then the response status code should be 404

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/lorem"
    Then the response status code should be 200
    And the JSON node "status" should be equal to "unpublished"
