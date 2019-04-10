Feature: Listing single route by slug or name
  In order to list route by slug or name
  As a HTTP Client
  I want to be able to render its url

  Scenario: Listing single route's url by slug
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Test route with Articles",
          "slug": "test-route-with-articles",
          "type": "collection",
          "templateName": "route_articles.html.twig"
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Test route with Articles"
    And the JSON node slug should be equal to "test-route-with-articles"
    And the JSON node staticPrefix should be equal to "/test-route-with-articles"
    When I go to "/test-route-with-articles"
    Then the response status code should be 200
    And I should not see "Abstract html test"
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/push" with body:
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
      "keywords":[],
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
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v1/packages/6/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "route":7,
              "is_published_fbia":false,
              "published":true
            }
          ]
      }
     """
    Then the response status code should be 201
    When I go to "/test-route-with-articles"
    Then the response status code should be 200
    And I should see "Abstract html test"
