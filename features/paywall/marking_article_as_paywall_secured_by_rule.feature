@paywall @rules
Feature: Marking article as paywall-secured using rules
  In order to make the article paywall-secured via rules
  As a HTTP Client
  I want to be able to create a rule which will mark article as paywall-secured

  Scenario: Marking an article as paywall-secured
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v1/organization/rules/" with body:
     """
      {
        "rule":{
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
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v1/rules/" with body:
     """
      {
        "rule":{
          "name":"Mark articles as paywall-secured",
          "description":"Mark articles as paywall-secured description",
          "priority":1,
          "expression":"article.getLocale() matches \"/en/\"",
          "configuration":[
            {
              "key":"paywallSecured",
              "value":true
            }
          ]
        }
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v1/rules/" with body:
     """
      {
        "rule":{
          "name":"Publish articles",
          "description":"Publish articles description",
          "priority":1,
          "expression":"article.getLocale() matches \"/en/\"",
          "configuration":[
            {
              "key":"published",
              "value":true
            },
            {
              "key":"route",
              "value":7
            }
          ]
        }
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route":{
          "name":"article",
          "type":"content"
        }
      }
     """
    Then the response status code should be 201
    And the Json node "id" should be equal to "7"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/content/push" with body:
     """
     {
        "language":"en",
        "slugline":"abstract-html-test",
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
    Then I send a "GET" request to "/api/{version}/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the Json node "isPublishable" should be true
    And the Json node "isPublishedFBIA" should be false
    And the Json node "publishedAt" should not be null
    And the Json node "route.id" should be equal to "7"
    And the Json node "status" should be equal to "published"
    And the Json node "paywallSecured" should be true
