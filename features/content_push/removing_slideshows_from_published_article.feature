@content_push
Feature: Removing existing slideshow from the already published article on correction
  If the slideshow is not needed in the article anymore
  As a HTTP Client
  I should be able to remove slideshow from already published article

  Scenario: Remove existing slideshow from the already published article
    Given I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "language":"en",
      "slugline":"abstract-html-test-without-slideshow",
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
                  "href":"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http"
                },
                "4-3":{
                  "height":533,
                  "mimetype":"image/jpeg",
                  "width":800,
                  "media":"1234567890987654321b",
                  "href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http"
                },
                "original":{
                  "height":2667,
                  "mimetype":"image/jpeg",
                  "width":4000,
                  "media":"1234567890987654321c",
                  "href":"http://localhost:3000/api/upload/1234567890987654321c/raw?_schema=http"
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
                  "href":"http://localhost:3000/api/upload/2234567890987654321a/raw?_schema=http"
                },
                "4-3":{
                  "height":533,
                  "mimetype":"image/jpeg",
                  "width":800,
                  "media":"2234567890987654321b",
                  "href":"http://localhost:3000/api/upload/2234567890987654321b/raw?_schema=http"
                },
                "original":{
                  "height":2667,
                  "mimetype":"image/jpeg",
                  "width":4000,
                  "media":"2234567890987654321c",
                  "href":"http://localhost:3000/api/upload/2234567890987654321c/raw?_schema=http"
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
      "headline":"Article with added slideshow",
      "service":[
        {
          "name":"Australian General News",
          "code":"a"
        }
      ],
      "description_html":"<p><b><u>some abstract text</u></b></p>",
      "located":"Warsaw",
      "pubstatus":"usable",
      "associations":{
        "slideshow1--1":{
          "renditions":{
            "16-9":{
              "height":720,
              "mimetype":"image/jpeg",
              "width":1079,
              "media":"1234567890987654321a",
              "href":"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http"
            },
            "4-3":{
              "height":533,
              "mimetype":"image/jpeg",
              "width":800,
              "media":"1234567890987654321b",
              "href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http"
            },
            "original":{
              "height":2667,
              "mimetype":"image/jpeg",
              "width":4000,
              "media":"1234567890987654321c",
              "href":"http://localhost:3000/api/upload/1234567890987654321c/raw?_schema=http"
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
        "slideshow1--2":{
          "renditions":{
            "16-9":{
              "height":720,
              "mimetype":"image/jpeg",
              "width":1079,
              "media":"2234567890987654321a",
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw?_schema=http"
            },
            "4-3":{
              "height":533,
              "mimetype":"image/jpeg",
              "width":800,
              "media":"2234567890987654321b",
              "href":"http://localhost:3000/api/upload/2234567890987654321b/raw?_schema=http"
            },
            "original":{
              "height":2667,
              "mimetype":"image/jpeg",
              "width":4000,
              "media":"2234567890987654321c",
              "href":"http://localhost:3000/api/upload/2234567890987654321c/raw?_schema=http"
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
      }
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
              "route": 3
            }
          ]
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test-without-slideshow"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | media[0].image.asset_id                 | 1234567890987654321c                   |
      | media[0].renditions[0].name            | 16-9                                   |
      | media[0].renditions[0].image.asset_id   | 1234567890987654321a                   |
      | media[0].renditions[1].name            | 4-3                                    |
      | media[0].renditions[1].image.asset_id   | 1234567890987654321b                   |
      | media[0].renditions[2].name            | original                               |
      | media[0].renditions[2].image.asset_id   | 1234567890987654321c                   |
      | media[1].image.asset_id                 | 2234567890987654321c                   |
      | media[1].renditions[0].name            | 16-9                                   |
      | media[1].renditions[0].image.asset_id   | 2234567890987654321a                   |
      | media[1].renditions[1].name            | 4-3                                    |
      | media[1].renditions[1].image.asset_id   | 2234567890987654321b                   |
      | media[1].renditions[2].name            | original                               |
      | media[1].renditions[2].image.asset_id   | 2234567890987654321c                   |
      | slideshows[0].code                     | slideshow1                             |
      | slideshows[0].id                       | 1                                      |
      | _links.slideshows.href                 | /api/v2/content/slideshows/6           |

    Given I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "language":"en",
      "slugline":"abstract-html-test-without-slideshow",
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

      },
      "version":"2",
      "byline":"ADmin",
      "keywords":[
        "keyword1",
        "keyword2"
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
      "headline":"Article with added slideshow",
      "service":[
        {
          "name":"Australian General News",
          "code":"a"
        }
      ],
      "description_html":"<p><b><u>some abstract text</u></b></p>",
      "located":"Warsaw",
      "pubstatus":"usable",
      "associations":{}
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test-without-slideshow"
    Then the response status code should be 200
    And the JSON node "slideshows" should have 0 elements
    And the JSON node "media" should have 0 elements
