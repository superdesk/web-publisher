@paywall @rules
Feature: Marking article as paywall-secured using rules
  In order to make the article paywall-secured via rules
  As a HTTP Client
  I want to be able to create a rule which will mark article as paywall-secured

  Scenario: Marking an article as paywall-secured
    Given I am authenticated as "test.user"
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
    Then I send a "POST" request to "/api/v2/rules/" with body:
     """
      {
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
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/rules/" with body:
     """
      {
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
    Then I send a "POST" request to "/api/v2/content/push" with body:
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
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the Json node "is_publishable" should be true
    And the Json node "is_published_fbia" should be false
    And the Json node "published_at" should not be null
    And the Json node "route.id" should be equal to "7"
    And the Json node "status" should be equal to "published"
    And the Json node "paywall_secured" should be true

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "DELETE" request to "/api/v2/rules/2"
    Then the response status code should be 204

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "DELETE" request to "/api/v2/rules/3"
    Then the response status code should be 204


    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/rules/" with body:
     """
      {
          "name":"Publish articles",
          "description":"Set paywall protection on article with disabled on tenant",
          "priority":1,
          "expression":"article.getLocale() matches \"/en/\"",
          "configuration":[
            {
              "key":"paywallSecured",
              "value":true
            },
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
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/content/push" with body:
     """
     {
        "language":"en",
        "slugline":"abstract-html-test-paywalled",
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
        "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf1",
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
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test-paywalled"
    Then the response status code should be 200
    And the Json node "is_publishable" should be true
    And the Json node "is_published_fbia" should be false
    And the Json node "published_at" should not be null
    And the Json node "route.id" should be equal to "7"
    And the Json node "status" should be equal to "published"
    And the Json node "paywall_secured" should be true
