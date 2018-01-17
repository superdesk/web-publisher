@rules
Feature: Adding article to bucket based on organization and tenant rules
  In order to publish an article on FBIA
  As a HTTP Client
  I want to be able to add the article to the bucket

  Scenario: Adding article to the bucket
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/content/lists/" with body:
     """
      {
        "content_list": {
          "name": "Example bucket",
          "type": "bucket"
        }
      }
    """
    Then the response status code should be 201
    And the JSON node "id" should be equal to "1"
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/organization/rules/" with body:
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
    And the Json node "id" should be equal to "6"
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/rules/" with body:
     """
      {
        "rule":{
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"article.getMetadataByKey(\"located\") matches \"/Sydney/\"",
          "configuration":[
            {
              "key":"route",
              "value":6
            }
          ]
        }
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/rules/" with body:
     """
      {
        "rule":{
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"article.getMetadataByKey(\"located\") matches \"/Sydney/\"",
          "configuration":[
            {
              "key":"fbia",
              "value":true
            }
          ]
        }
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/content/push" with body:
     """
     {"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the Json node "isPublishable" should be true
    And the Json node "isPublishedFBIA" should be true
    And the Json node "publishedAt" should not be null
    And the Json node "route.id" should be equal to "6"
    And the Json node "status" should be equal to "published"
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/content/lists/1/items/"
    And the Json node "total" should be equal to "1"
