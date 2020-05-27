@content_lists
Feature: Do not add articles to automatic content lists
  In order to disallow adding articles to automatic content lists without criteria set
  As a HTTP Client
  I want to be able to push new content and do not see it in content list

  Scenario: Push new article and do not add it to automatic content list
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/lists/" with body:
     """
      {
          "name": "Example automatic list",
          "type": "automatic"
      }
    """
    Then the response status code should be 201
    And the JSON node "id" should be equal to "1"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/organization/rules/" with body:
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
    Then I send a "POST" request to "/api/v2/content/routes/" with body:
     """
      {
          "name":"article",
          "type":"content"
      }
     """
    Then the response status code should be 201
    And the Json node "id" should be equal to "7"
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/rules/" with body:
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

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/content/push" with body:
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
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test"

    Then the response status code should be 200
    And the Json node "is_publishable" should be true
    And the Json node "is_published_fbia" should be false
    And the Json node "published_at" should not be null
    And the Json node "route.id" should be equal to "6"
    And the Json node "status" should be equal to "published"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/lists/1/items/"
    And the Json node "total" should be equal to "0"
