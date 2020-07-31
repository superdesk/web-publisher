Feature: Handle migrated articles redirects

  Scenario: Push content and create redirect automatically to previous article url
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "language":"en",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-09-23T09:11:28+0000",
      "description_text":"some abstract text",
      "version":"2",
      "byline":"ADmin",
      "keywords":[],
      "extra":{
        "original_article_url": "https://example.com/abstract-html-test"
      },
      "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0",
      "priority":6,
      "urgency":3,
      "type":"text",
      "headline":"Abstract html test",
      "description_html":"<p><b><u>some abstract text</u></b></p>",
      "located":"Sydney",
      "pubstatus":"usable",
      "firstPublished": "2016-09-23T09:11:28+0000"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/6/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true,
              "route": 6
            }
          ]
      }
     """
    Then the response status code should be 201

    When I send a GET request to "/abstract-html-test"
    Then the response status code should be 301
    And the header "location" should be equal to "/news/sports/abstract-html-test"
