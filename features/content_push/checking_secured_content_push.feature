@content_push
Feature: Checking if not authorized content push is rejected
  In order to process content push
  As a HTTP Client
  I want to be able to check if request is signed correctly

  Scenario: Setting secret token in organization and sending secured content push
    When I run "swp:organization:create Organization1 --env=test -u --secretToken secret_token" command
    Then I should see "Organization Organization1 (code: 123456) has been updated and is enabled!" in the output
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    When I add "x-superdesk-signature" header equal to "sha1=04ed4c6f9227b55dead294034c08a7a30d3cbc99"
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

  Scenario: Setting wrong secret token in organization and sending secured content push
    When I run "swp:organization:create Organization1 --env=test -u --secretToken secret_token" command
    Then I should see "Organization Organization1 (code: 123456) has been updated and is enabled!" in the output
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    When I add "x-superdesk-signature" header equal to "sha1=04ed4c6f9227b55dead294034c08a7a30d3cbc99"
    And I send a "POST" request to "/api/{version}/content/push" with body:
    """
    {
      "language":"en",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-09-23T09:11:28+0000",
      "description_text":"some abstract text",
      "version":"2",
      "byline":"ADmin",
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
    Then the response status code should be 401
