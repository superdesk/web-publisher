@targeted_publishing
Feature: Evaluate and ensure the package has been published under specific tenants,
  based on destinations and rules.

  Scenario: Create rules for one tenant and destinations for the second one
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "http://localhost/api/v2/organization/rules/" with body:
     """
      {
          "name":"Test rule",
          "description":"Test rule description",
          "priority":1,
          "expression":"package.getLocated() matches \"/Sydney/\"",
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
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "http://localhost/api/v2/rules/" with body:
     """
      {
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"article.getMetadataByKey(\"located\") matches \"/Sydney/\"",
          "configuration":[
            {
              "key":"route",
              "value":6
            },
            {
              "key":"published",
              "value":true
            }
          ]
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.client2"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "http://client2.localhost/api/v2/content/routes/" with body:
     """
      {
          "name": "My route",
          "type": "collection"
      }
    """
    Then the response status code should be 201
    And the JSON node "id" should be equal to "7"
    And I am authenticated as "test.client2"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "http://localhost/api/v2/organization/rules/evaluate" with body:
     """
     {"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}
     """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenants[0].tenant.code  | 123abc |
      | tenants[0].route.id     | 6      |
    And the JSON node "tenants[0].published" should be true
    And the JSON node "tenants[0].is_published_fbia" should be false
    And the JSON node "tenants[1]" should not exist
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "http://localhost/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"678iop",
          "route":7,
          "is_published_fbia":false,
          "published":true,
          "packageGuid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0"
      }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "http://localhost/api/v2/organization/rules/evaluate" with body:
     """
     {"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}
     """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenants[0].tenant.code  | 678iop |
      | tenants[1].tenant.code  | 123abc |
      | tenants[0].route.id     | 7      |
      | tenants[1].route.id     | 6      |
    And the JSON node "tenants[0].published" should be true
    And the JSON node "tenants[0].is_published_fbia" should be false
    And the JSON node "tenants[1].published" should be true
    And the JSON node "tenants[1].is_published_fbia" should be false
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "http://localhost/api/v2/content/push" with body:
    """
    {
      "language":"en",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-09-23T09:11:28+0000",
      "description_text":"some abstract text",
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

      ],
      "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0",
      "priority":6,
      "subject":[
        {
          "name":"lawyer",
          "code":"02002001"
        }
      ],
      "source": "superdesk publisher",
      "urgency":3,
      "type":"text",
      "headline":"Abstract html test",
      "service":[
        {
          "name":"Australian General News",
          "code":"a"
        }
      ],
      "description_html":"<p><b><u>some abstract text</u></b></p>",
      "located":"Sydney",
      "pubstatus":"usable"
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "http://localhost/api/v2/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | slug                          | abstract-html-test  |
      | route.id                      | 6                   |
      | status                        | published           |
      | sources[0].article_source.name | superdesk publisher |
    And I am authenticated as "test.client2"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "http://client2.localhost/api/v2/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | slug                          | abstract-html-test  |
      | route.id                      | 7                   |
      | status                        | published           |
      | sources[0].article_source.name | superdesk publisher |
