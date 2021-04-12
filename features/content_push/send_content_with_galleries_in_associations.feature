@content_push
Feature: Handling the custom media fields
  In order to be able to display galleries inside the article body
  As a HTTP Client
  I want to able to receive and parse the request with custom media fields payload

  Scenario: Correct existing article with sideshow with new article and the same slideshows
    Given I am authenticated as "test.user"
    And the current date time is "2019-03-10 09:00"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/webhooks/" with body:
     """
      {
          "url": "http://localhost:3000/article-update",
          "events": [
              "article[updated]"
          ],
          "enabled": "1"
      }
    """
    Then  the response status code should be 201

    And I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
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
              "located":"Porto",
              "order":0
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
              "located":"Porto",
              "order":1
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
          "located":"Porto",
          "order":0
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
          "located":"Porto",
          "order":1
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

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/slideshows/6/1/items/"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 2
    And the JSON nodes should contain:
      | _embedded._items[0].article_media.image.asset_id   | 1234567890987654321c |
      | _embedded._items[1].article_media.image.asset_id   | 2234567890987654321c |
      | _embedded._items[0].position                       | 0                    |
      | _embedded._items[1].position                       | 1                    |

    Given I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
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
              "located":"Porto",
              "order":0
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
              "located":"Porto",
              "order":1
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
      "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf2",
      "evolvedfrom":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf1",
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
          "located":"Porto",
          "order":0
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
          "located":"Porto",
          "order":1
        },
        "featuremedia":{
          "subject":[
            {
              "code":"05004000",
              "name":"preschool"
            }
          ],
          "type":"picture",
          "usageterms":"indefinite-usage",
          "priority":6,
          "renditions":{
            "original":{
              "width":2048,
              "mimetype":"image/jpeg",
              "poi":{
                "x":1228,
                "y":586
              },
              "media":"20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg",
              "height":1365,
              "href":"http://localhost:3000/api/upload/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea/raw"
            }
          },
          "place":[

          ],
          "pubstatus":"usable",
          "slugline":"gradac",
          "firstcreated":"2017-01-11T14:32:58+0000",
          "mimetype":"image/jpeg",
          "service":[
            {
              "code":"news",
              "name":"News"
            }
          ],
          "byline":"Ljub. Z. Rankovi\u0107",
          "urgency":3,
          "language":"en",
          "headline":"Smoke on the water",
          "versioncreated":"2017-01-11T14:52:05+0000",
          "description_text":"Smoke on the water on River Gradac\u00a0",
          "guid":"tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09",
          "body_text":"Gradac alt text",
          "version":"5",
          "copyrightnotice": "Notice",
          "copyrightholder": "Holder"
        }
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test-without-slideshow"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | media[1].image.asset_id                 | 1234567890987654321c                   |
      | media[1].renditions[0].name            | 16-9                                   |
      | media[1].renditions[0].image.asset_id   | 1234567890987654321a                   |
      | media[1].renditions[1].name            | 4-3                                    |
      | media[1].renditions[1].image.asset_id   | 1234567890987654321b                   |
      | media[1].renditions[2].name            | original                               |
      | media[1].renditions[2].image.asset_id   | 1234567890987654321c                   |
      | media[2].image.asset_id                 | 2234567890987654321c                   |
      | media[2].renditions[0].name            | 16-9                                   |
      | media[2].renditions[0].image.asset_id   | 2234567890987654321a                   |
      | media[2].renditions[1].name            | 4-3                                    |
      | media[2].renditions[1].image.asset_id   | 2234567890987654321b                   |
      | media[2].renditions[2].name            | original                               |
      | media[2].renditions[2].image.asset_id   | 2234567890987654321c                   |
      | slideshows[0].code                     | slideshow1                             |
      | slideshows[0].id                       | 2                                      |
      | _links.slideshows.href                 | /api/v2/content/slideshows/6           |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/slideshows/6/2"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | code                   | slideshow1                              |
      | article.id             | 6                                       |
      | id                     | 2                                       |
      | _links.items.href      | /api/v2/content/slideshows/6/2/items/   |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/slideshows/6/2/items/"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 2
    And the JSON nodes should contain:
      | _embedded._items[0].article_media.image.asset_id   | 1234567890987654321c |
      | _embedded._items[1].article_media.image.asset_id   | 2234567890987654321c |
      | _embedded._items[0].position                       | 0                    |
      | _embedded._items[1].position                       | 1                    |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/slideshows/6/1/items/"
    Then the response status code should be 404

    And The payload received by "http://localhost:3000/article-update-check" webhook should be equal to:
    """
    {
      "extra": [],
      "id": 6,
      "title": "Article with added slideshow",
      "body": "<p>some html body<\/p>",
      "slug": "abstract-html-test-without-slideshow",
      "published_at": "2019-03-10T09:00:00+00:00",
      "status": "published",
      "route": {
        "requirements": {
          "slug": "[a-zA-Z0-9*\\-_]+"
        },
        "id": 3,
        "static_prefix": "\/news",
        "variable_pattern": "\/{slug}",
        "children": [
          {
            "requirements": {
              "slug": "[a-zA-Z0-9*\\-_]+"
            },
            "id": 6,
            "static_prefix": "\/news\/sports",
            "variable_pattern": "\/{slug}",
            "parent": 3,
            "children": [],
            "lft": 4,
            "rgt": 5,
            "level": 1,
            "type": "collection",
            "cache_time_in_seconds": 0,
            "name": "sports",
            "slug": "sports",
            "position": 0,
            "articles_count": 0,
            "paywall_secured": false,
            "_links": {
              "self": {
                "href": "\/api\/v2\/content\/routes\/6"
              },
              "parent": {
                "href": "\/api\/v2\/content\/routes\/3"
              }
            }
          }
        ],
        "lft": 3,
        "rgt": 6,
        "level": 0,
        "type": "collection",
        "cache_time_in_seconds": 0,
        "name": "news",
        "slug": "news",
        "position": 1,
        "articles_count": 0,
        "paywall_secured": false,
        "_links": {
          "self": {
            "href": "\/api\/v2\/content\/routes\/3"
          }
        }
      },
      "is_publishable": true,
      "metadata": {
        "subject": [
          {
            "name": "lawyer",
            "code": "02002001"
          }
        ],
        "urgency": 3,
        "priority": 6,
        "located": "Warsaw",
        "place": [
          {
            "country": "Australia",
            "world_region": "Oceania",
            "state": "Australian Capital Territory",
            "qcode": "ACT",
            "name": "ACT",
            "group": "Australia"
          }
        ],
        "service": [
          {
            "name": "Australian General News",
            "code": "a"
          }
        ],
        "type": "text",
        "byline": "ADmin",
        "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf2",
        "language": "en"
      },
      "lead": "some abstract text",
      "code": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf2",
      "sources": [],
      "slideshows": {
        "1": {
          "id": 2,
          "code": "slideshow1",
          "items": [
            {
              "article_media": {
                "id": 4,
                "image": {
                  "id": 8,
                  "file_extension": "jpg",
                  "asset_id": "1234567890987654321c",
                  "width": 480,
                  "height": 720,
                  "length": 25
                },
                "description": "test image",
                "by_line": "Pawe\u0142 Miko\u0142ajczuk",
                "alt_text": "test image",
                "usage_terms": "indefinite-usage",
                "renditions": [
                  {
                    "width": 1079,
                    "height": 720,
                    "name": "16-9",
                    "id": 8,
                    "image": {
                      "id": 6,
                      "file_extension": "jpg",
                      "asset_id": "1234567890987654321a",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321a\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321a.jpg"
                      }
                    }
                  },
                  {
                    "width": 800,
                    "height": 533,
                    "name": "4-3",
                    "id": 9,
                    "image": {
                      "id": 7,
                      "file_extension": "jpg",
                      "asset_id": "1234567890987654321b",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321b\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321b.jpg"
                      }
                    }
                  },
                  {
                    "width": 4000,
                    "height": 2667,
                    "name": "original",
                    "id": 10,
                    "image": {
                      "id": 8,
                      "file_extension": "jpg",
                      "asset_id": "1234567890987654321c",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321c\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321c.jpg"
                      }
                    }
                  },
                  {
                    "width": 4000,
                    "height": 2667,
                    "name": "thumbnail",
                    "id": 10,
                    "image": {
                      "id": 8,
                      "file_extension": "jpg",
                      "asset_id": "1234567890987654321c",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321c\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321c.jpg"
                      }
                    }
                  },
                  {
                    "width": 4000,
                    "height": 2667,
                    "name": "viewImage",
                    "id": 10,
                    "image": {
                      "id": 8,
                      "file_extension": "jpg",
                      "asset_id": "1234567890987654321c",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321c\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321c.jpg"
                      }
                    }
                  }
                ],
                "headline": "test image",
                "_links": {
                  "download": {
                    "href": "\/media\/1234567890987654321c.jpg"
                  }
                }
              },
              "position": 0
            },
            {
              "article_media": {
                "id": 5,
                "image": {
                  "id": 11,
                  "file_extension": "jpg",
                  "asset_id": "2234567890987654321c",
                  "width": 480,
                  "height": 720,
                  "length": 25
                },
                "description": "test image 2",
                "by_line": "Pawe\u0142 Miko\u0142ajczuk",
                "alt_text": "test image",
                "usage_terms": "indefinite-usage",
                "renditions": [
                  {
                    "width": 1079,
                    "height": 720,
                    "name": "16-9",
                    "id": 11,
                    "image": {
                      "id": 9,
                      "file_extension": "jpg",
                      "asset_id": "2234567890987654321a",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321a\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321a.jpg"
                      }
                    }
                  },
                  {
                    "width": 800,
                    "height": 533,
                    "name": "4-3",
                    "id": 12,
                    "image": {
                      "id": 10,
                      "file_extension": "jpg",
                      "asset_id": "2234567890987654321b",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321b\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321b.jpg"
                      }
                    }
                  },
                  {
                    "width": 4000,
                    "height": 2667,
                    "name": "original",
                    "id": 13,
                    "image": {
                      "id": 11,
                      "file_extension": "jpg",
                      "asset_id": "2234567890987654321c",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321c\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321c.jpg"
                      }
                    }
                  },
                  {
                    "width": 4000,
                    "height": 2667,
                    "name": "thumbnail",
                    "id": 13,
                    "image": {
                      "id": 11,
                      "file_extension": "jpg",
                      "asset_id": "2234567890987654321c",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321c\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321c.jpg"
                      }
                    }
                  },
                  {
                    "width": 4000,
                    "height": 2667,
                    "name": "viewImage",
                    "id": 13,
                    "image": {
                      "id": 11,
                      "file_extension": "jpg",
                      "asset_id": "2234567890987654321c",
                      "width": 480,
                      "height": 720,
                      "length": 25
                    },
                    "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321c\/raw?_schema=http",
                    "_links": {
                      "public_url": {
                        "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321c.jpg"
                      }
                    }
                  }
                ],
                "headline": "test image",
                "_links": {
                  "download": {
                    "href": "\/media\/2234567890987654321c.jpg"
                  }
                }
              },
              "position": 1
            }
          ],
          "created_at": "2019-03-10T09:00:00+00:00",
          "updated_at": "2019-03-10T09:00:00+00:00",
          "_links": {
            "items": {
              "href": "\/api\/v2\/content\/slideshows\/6\/2\/items\/"
            }
          }
        }
      },
      "previous_relative_urls": [],
      "created_at": "2019-03-10T09:00:00+00:00",
      "updated_at": "2019-03-10T09:00:00+00:00",
      "authors": [],
      "keywords": [
        {
          "slug": "keyword1",
          "name": "keyword1"
        },
        {
          "slug": "keyword2",
          "name": "keyword2"
        }
      ],
      "media": [
        {
          "id": 3,
          "image": {
            "id": 12,
            "file_extension": "png",
            "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
            "width": 255,
            "height": 76,
            "length": 4
          },
          "description": "Smoke on the water on River Gradac\u00a0",
          "by_line": "Ljub. Z. Rankovi\u0107",
          "alt_text": "Gradac alt text",
          "usage_terms": "indefinite-usage",
          "renditions": [
            {
              "width": 2048,
              "height": 1365,
              "name": "original",
              "id": 7,
              "image": {
                "id": 13,
                "file_extension": "png",
                "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
                "width": 255,
                "height": 76,
                "length": 4
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea\/raw",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
                }
              }
            },
            {
              "width": 2048,
              "height": 1365,
              "name": "thumbnail",
              "id": 7,
              "image": {
                "id": 13,
                "file_extension": "png",
                "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
                "width": 255,
                "height": 76,
                "length": 4
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea\/raw",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
                }
              }
            },
            {
              "width": 2048,
              "height": 1365,
              "name": "viewImage",
              "id": 7,
              "image": {
                "id": 13,
                "file_extension": "png",
                "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
                "width": 255,
                "height": 76,
                "length": 4
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea\/raw",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
                }
              }
            }
          ],
          "headline": "Smoke on the water",
          "copyright_holder": "Holder",
          "copyright_notice": "Notice",
          "_links": {
            "download": {
              "href": "\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
            }
          }
        },
        {
          "id": 4,
          "image": {
            "id": 8,
            "file_extension": "jpg",
            "asset_id": "1234567890987654321c",
            "width": 480,
            "height": 720,
            "length": 25
          },
          "description": "test image",
          "by_line": "Pawe\u0142 Miko\u0142ajczuk",
          "alt_text": "test image",
          "usage_terms": "indefinite-usage",
          "renditions": [
            {
              "width": 1079,
              "height": 720,
              "name": "16-9",
              "id": 8,
              "image": {
                "id": 6,
                "file_extension": "jpg",
                "asset_id": "1234567890987654321a",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321a\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321a.jpg"
                }
              }
            },
            {
              "width": 800,
              "height": 533,
              "name": "4-3",
              "id": 9,
              "image": {
                "id": 7,
                "file_extension": "jpg",
                "asset_id": "1234567890987654321b",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321b\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321b.jpg"
                }
              }
            },
            {
              "width": 4000,
              "height": 2667,
              "name": "original",
              "id": 10,
              "image": {
                "id": 8,
                "file_extension": "jpg",
                "asset_id": "1234567890987654321c",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321c\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321c.jpg"
                }
              }
            },
            {
              "width": 4000,
              "height": 2667,
              "name": "thumbnail",
              "id": 10,
              "image": {
                "id": 8,
                "file_extension": "jpg",
                "asset_id": "1234567890987654321c",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321c\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321c.jpg"
                }
              }
            },
            {
              "width": 4000,
              "height": 2667,
              "name": "viewImage",
              "id": 10,
              "image": {
                "id": 8,
                "file_extension": "jpg",
                "asset_id": "1234567890987654321c",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/1234567890987654321c\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/1234567890987654321c.jpg"
                }
              }
            }
          ],
          "headline": "test image",
          "_links": {
            "download": {
              "href": "\/media\/1234567890987654321c.jpg"
            }
          }
        },
        {
          "id": 5,
          "image": {
            "id": 11,
            "file_extension": "jpg",
            "asset_id": "2234567890987654321c",
            "width": 480,
            "height": 720,
            "length": 25
          },
          "description": "test image 2",
          "by_line": "Pawe\u0142 Miko\u0142ajczuk",
          "alt_text": "test image",
          "usage_terms": "indefinite-usage",
          "renditions": [
            {
              "width": 1079,
              "height": 720,
              "name": "16-9",
              "id": 11,
              "image": {
                "id": 9,
                "file_extension": "jpg",
                "asset_id": "2234567890987654321a",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321a\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321a.jpg"
                }
              }
            },
            {
              "width": 800,
              "height": 533,
              "name": "4-3",
              "id": 12,
              "image": {
                "id": 10,
                "file_extension": "jpg",
                "asset_id": "2234567890987654321b",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321b\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321b.jpg"
                }
              }
            },
            {
              "width": 4000,
              "height": 2667,
              "name": "original",
              "id": 13,
              "image": {
                "id": 11,
                "file_extension": "jpg",
                "asset_id": "2234567890987654321c",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321c\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321c.jpg"
                }
              }
            },
            {
              "width": 4000,
              "height": 2667,
              "name": "thumbnail",
              "id": 13,
              "image": {
                "id": 11,
                "file_extension": "jpg",
                "asset_id": "2234567890987654321c",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321c\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321c.jpg"
                }
              }
            },
            {
              "width": 4000,
              "height": 2667,
              "name": "viewImage",
              "id": 13,
              "image": {
                "id": 11,
                "file_extension": "jpg",
                "asset_id": "2234567890987654321c",
                "width": 480,
                "height": 720,
                "length": 25
              },
              "preview_url": "http:\/\/localhost:3000\/api\/upload\/2234567890987654321c\/raw?_schema=http",
              "_links": {
                "public_url": {
                  "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/2234567890987654321c.jpg"
                }
              }
            }
          ],
          "headline": "test image",
          "_links": {
            "download": {
              "href": "\/media\/2234567890987654321c.jpg"
            }
          }
        }
      ],
      "feature_media": {
        "id": 3,
        "image": {
          "id": 12,
          "file_extension": "png",
          "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
          "width": 255,
          "height": 76,
          "length": 4
        },
        "description": "Smoke on the water on River Gradac\u00a0",
        "by_line": "Ljub. Z. Rankovi\u0107",
        "alt_text": "Gradac alt text",
        "usage_terms": "indefinite-usage",
        "renditions": [
          {
            "width": 2048,
            "height": 1365,
            "name": "original",
            "id": 7,
            "image": {
              "id": 13,
              "file_extension": "png",
              "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
              "width": 255,
              "height": 76,
              "length": 4
            },
            "preview_url": "http:\/\/localhost:3000\/api\/upload\/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea\/raw",
            "_links": {
              "public_url": {
                "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
              }
            }
          },
          {
            "width": 2048,
            "height": 1365,
            "name": "thumbnail",
            "id": 7,
            "image": {
              "id": 13,
              "file_extension": "png",
              "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
              "width": 255,
              "height": 76,
              "length": 4
            },
            "preview_url": "http:\/\/localhost:3000\/api\/upload\/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea\/raw",
            "_links": {
              "public_url": {
                "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
              }
            }
          },
          {
            "width": 2048,
            "height": 1365,
            "name": "viewImage",
            "id": 7,
            "image": {
              "id": 13,
              "file_extension": "png",
              "asset_id": "20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea",
              "width": 255,
              "height": 76,
              "length": 4
            },
            "preview_url": "http:\/\/localhost:3000\/api\/upload\/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea\/raw",
            "_links": {
              "public_url": {
                "href": "http:\/\/localhost\/uploads\/swp\/123456\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
              }
            }
          }
        ],
        "headline": "Smoke on the water",
        "copyright_holder": "Holder",
        "copyright_notice": "Notice",
        "_links": {
          "download": {
            "href": "\/media\/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
          }
        }
      },
      "is_published_fbia": false,
      "article_statistics": {
        "impressions_number": 0,
        "page_views_number": 0,
        "internal_click_rate": 0,
        "created_at": "2019-03-10T09:00:00+00:00",
        "updated_at": "2019-03-10T09:00:00+00:00"
      },
      "comments_count": 0,
      "is_published_to_apple_news": false,
      "tenant": {
        "id": 1,
        "domain_name": "localhost",
        "name": "Default tenant",
        "code": "123abc",
        "amp_enabled": true,
        "pwa_config":[],
        "_links": {
          "self": {
            "href": "\/api\/v2\/tenants\/123abc"
          }
        },
        "default_language": "",
        "fbia_enabled": false,
        "paywall_enabled": false
      },
      "paywall_secured": false,
      "content_lists": [],
      "_links": {
        "self": {
          "href": "\/api\/v2\/content\/articles\/abstract-html-test-without-slideshow"
        },
        "online": {
          "href": "\/news\/abstract-html-test-without-slideshow"
        },
        "related": {
          "href": "\/api\/v2\/content\/articles\/6\/related\/"
        },
        "slideshows": {
          "href": "\/api\/v2\/content\/slideshows\/6"
        }
      }
    }
    """

    And I set "abstract-html-test-without-slideshow" as a current article in the context

    And I render a template with content:
"""
{% gimmelist slideshow from slideshows with { article: gimme.article } %}
{{ slideshow.code }}
{% gimmelist slideshowItem from slideshowItems with { article: gimme.article, slideshow: slideshow } %}
{{slideshowItem.articleMedia.key}}
{% endgimmelist %}
{% endgimmelist %}
"""
    Then rendered template should be equal to:
    """
slideshow1
tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6
tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a2

    """
