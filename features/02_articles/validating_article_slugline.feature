@articles
Feature: Validating if article slugline is created out of the package headline
  when slugline metadata is is not present in request payload.
  In order to publish an article and access it via slug
  As a HTTP Client
  I want to be able to check if the article slug was generated properly

  Scenario: Submitting request payload in ninjs format and publishing a package
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/content/push" with body:
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
    Then I send a "POST" request to "/api/{version}/content/routes/" with body:
     """
      {
        "route":{
          "name":"article",
          "type":"content"
        }
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/packages/5/publish/" with body:
     """
      {
        "publish":{
          "destinations":[
            {
              "tenant":"123abc",
              "route":6,
              "fbia":false
            }
          ]
        }
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the JSON node "slug" should be equal to "abstract-html-test"
