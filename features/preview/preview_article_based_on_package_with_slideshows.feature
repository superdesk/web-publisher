@preview
Feature: Preview article under route based on package data with slideshows

  Scenario: Preview article based on package under selected route with slideshows
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route": {
          "name": "Simple test route",
          "slug": "simple-test-route",
          "type": "collection",
          "articlesTemplateName": "article_with_slideshow.html.twig"
        }
      }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/preview/package/generate_token/7" with body:
    """
    {
      "language":"en",
      "slugline":"abstract-html-test",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-05-25T10:23:15+0000",
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
      "extra_items":{
        "slideshow1": {
          "items":[
            {
              "renditions":{
                "16-9":{
                  "height":720,
                  "mimetype":"image/jpeg",
                  "width":1079,
                  "media":"1234567890987654321a",
                  "href":"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"
                },
                "4-3":{
                  "height":533,
                  "mimetype":"image/jpeg",
                  "width":800,
                  "media":"1234567890987654321b",
                  "href":"http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"
                },
                "original":{
                  "height":2667,
                  "mimetype":"image/jpeg",
                  "width":4000,
                  "media":"1234567890987654321c",
                  "href":"http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"
                }
              },
              "urgency":3,
              "body_text":"test image",
              "versioncreated":"2016-08-17T17:46:52+0000",
              "guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6",
              "byline":"Pawe\u0142 Miko\u0142ajczuk",
              "pubstatus":"usable",
              "language":"en",
              "version":"2",
              "description_text":"test image",
              "priority":6,
              "type":"picture",
              "service":[
                {
                  "name":"Australian General News",
                  "code":"a"
                }
              ],
              "usageterms":"indefinite-usage",
              "mimetype":"image/jpeg",
              "headline":"test image",
              "located":"Porto"
            },
            {
              "renditions":{
                "16-9":{
                  "height":720,
                  "mimetype":"image/jpeg",
                  "width":1079,
                  "media":"2234567890987654321a",
                  "href":"http://localhost:5000/api/upload/2234567890987654321a/raw?_schema=http"
                },
                "4-3":{
                  "height":533,
                  "mimetype":"image/jpeg",
                  "width":800,
                  "media":"2234567890987654321b",
                  "href":"http://localhost:5000/api/upload/2234567890987654321b/raw?_schema=http"
                },
                "original":{
                  "height":2667,
                  "mimetype":"image/jpeg",
                  "width":4000,
                  "media":"2234567890987654321c",
                  "href":"http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http"
                }
              },
              "urgency":3,
              "body_text":"test image",
              "versioncreated":"2016-08-17T17:46:52+0000",
              "guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a2",
              "byline":"Pawe\u0142 Miko\u0142ajczuk",
              "pubstatus":"usable",
              "language":"en",
              "version":"2",
              "description_text":"test image 2",
              "priority":6,
              "type":"picture",
              "service":[
                {
                  "name":"Australian General News",
                  "code":"a"
                }
              ],
              "usageterms":"indefinite-usage",
              "mimetype":"image/jpeg",
              "headline":"test image",
              "located":"Porto"
            }
          ]
        }
      },
      "version":"2",
      "byline":"ADmin",
      "keywords":[
        "keyword1",
        "keyword2"
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
      "headline":"testing correction",
      "service":[
        {
          "name":"Australian General News",
          "code":"a"
        }
      ],
      "description_html":"<p><b><u>some abstract text</u></b></p>",
      "located":"Warsaw",
      "pubstatus":"usable"
    }
    """
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    {
        "preview_url": "http:\/\/localhost\/preview\/publish\/package\/0123456789"
    }
    """
    When I go to "http://localhost/preview/publish/package/0123456789"
    Then the response status code should be 200
    And the response should contain "slideshow1"
    And the response should contain "http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"
    And the response should contain "http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http"
