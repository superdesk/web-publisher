@content_push
Feature: Checking if pushed package will be published on tenant with output channel set to Wordpress
  In order to process content push
  As a HTTP Client
  I want to be able to check if request is processed and forwarded correctly

  Scenario: Publishing content to tenant with wordpress configured as a output channel.
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/tenants/123abc" with body:
     """
      {
          "output_channel": {
            "type": "wordpress",
            "config": {
              "url": "http://localhost:3000",
              "authorization_key": "Basic YWRtaW46dTJnWiB1QTlpIFVkYXogZnVtMSAxQnNkIHpwV2c="
            }
          }
      }
    """
    Then the response status code should be 200
    And the JSON node "output_channel.type" should be equal to "wordpress"
    And the JSON node "output_channel.config.url" should be equal to "http://localhost:3000"
    And the JSON node "output_channel.config.authorizationKey" should be equal to "Basic YWRtaW46dTJnWiB1QTlpIFVkYXogZnVtMSAxQnNkIHpwV2c="
    When I am authenticated as "test.user"
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
        "test keyword"
      ],
      "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0",
      "priority":6,
      "urgency":3,
      "type":"text",
      "headline":"Abstract html test",
      "description_html":"<p><b><u>some abstract text</u></b></p>",
      "located":"Sydney",
      "pubstatus":"usable"
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | status | new |

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

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | status                              | published                          |
      | articles[0].external_article.id      | 1                                  |
      | articles[0].external_article.live_url | localhost:3000/wordpress/test_post |
      | articles[0].external_article.status  | publish                            |

    When I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v1/content/articles/6" with body:
     """
      {
          "status": "new"
      }
     """
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | status                  | new                                |
      | external_article.id      | 1                                  |
      | external_article.live_url | localhost:3000/wordpress/test_post |
      | external_article.status  | draft                              |
