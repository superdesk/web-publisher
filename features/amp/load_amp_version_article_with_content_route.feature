@amp
Feature: Rendering AMP HTML version of the content assigned to route of type "content"
  In order to render AMP HTML version of of content with route of type "content"
  As a HTTP Client
  I want to be able to see the valid AMP HTML page

  Scenario: Change route template
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Contact",
          "slug": "contact",
          "type": "content"
      }
    """
    Then the response status code should be 201
    And the JSON node "id" should be equal to "7"

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/tenants/123abc" with body:
     """
      {
          "ampEnabled": true
      }
    """
    Then the response status code should be 200

    And I am authenticated as "test.user"
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
    Then I send a "POST" request to "/api/v1/packages/6/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true
            }
          ]
      }
     """
    Then the response status code should be 201

    When I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v1/content/routes/7" with body:
     """
      {
          "content": 6
      }
     """
    Then the response status code should be 200

    When I go to "http://localhost/contact"
    Then the response status code should be 200
    And the response should contain "Abstract html test"
    
    When I go to "http://localhost/contact?amp"
    Then the response status code should be 200
    And the response should contain "AMP Demo Theme"
    And the response should contain "some html body"
    And the response should contain "Abstract html test"
