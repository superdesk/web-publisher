Feature: Skip blacklisted keywords

  Scenario: Push content and do not add blacklisted keywords
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/push" with body:
    """
    {
      "language":"en",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-09-23T09:11:28+0000",
      "description_text":"some abstract text",
      "version":"2",
      "byline":"ADmin",
      "keywords":[
        "sub_feature",
        "apps",
        "top_feature"
      ],
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
    Then I send a "POST" request to "/api/v1/packages/6/publish/" with body:
     """
      {
        "publish":{
          "destinations":[
            {
              "tenant":"123abc",
              "published":true
            }
          ]
        }
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the JSON node "keywords" should exist
    And the JSON node keywords" should have 2 elements
    And the JSON nodes should contain:
      | keywords[0].name   | sub_feature |
      | keywords[1].name   | apps        |
