@content_push
Feature: Handling the custom media fields
  In order to be able to display galleries inside the article body
  As a HTTP Client
  I want to able to receive and parse the request with custom media fields payload

  Scenario: Correct existing article with sideshow with new article and the same slideshows
    Given I am authenticated as "test.user"
    And the current date time is "2019-03-10 09:00"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/webhooks/" with body:
     """
      {
        "webhook": {
          "url": "http://localhost:3000/article-update",
          "events": [
              "article[updated]"
          ],
          "enabled": "1"
        }
      }
    """
    Then  the response status code should be 201

    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/push" with body:
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
        "slideshow1--2":{
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
      }
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
              "published":true,
              "route": 3
            }
          ]
        }
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/abstract-html-test-without-slideshow"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | media[0].image.assetId                 | 1234567890987654321c                   |
      | media[0].renditions[0].name            | 16-9                                   |
      | media[0].renditions[0].image.assetId   | 1234567890987654321a                   |
      | media[0].renditions[1].name            | 4-3                                    |
      | media[0].renditions[1].image.assetId   | 1234567890987654321b                   |
      | media[0].renditions[2].name            | original                               |
      | media[0].renditions[2].image.assetId   | 1234567890987654321c                   |
      | media[1].image.assetId                 | 2234567890987654321c                   |
      | media[1].renditions[0].name            | 16-9                                   |
      | media[1].renditions[0].image.assetId   | 2234567890987654321a                   |
      | media[1].renditions[1].name            | 4-3                                    |
      | media[1].renditions[1].image.assetId   | 2234567890987654321b                   |
      | media[1].renditions[2].name            | original                               |
      | media[1].renditions[2].image.assetId   | 2234567890987654321c                   |
      | slideshows[0].code                     | slideshow1                             |
      | slideshows[0].id                       | 1                                      |
      | _links.slideshows.href                 | /api/v1/content/slideshows/6           |

    Given I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 1234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321a  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321b  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                 |
      | media_id     | 2234567890987654321c  |
      | media        | @image.jpg            |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/push" with body:
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
        "slideshow1--2":{
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
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/abstract-html-test-without-slideshow"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | media[0].image.assetId                 | 1234567890987654321c                   |
      | media[0].renditions[0].name            | 16-9                                   |
      | media[0].renditions[0].image.assetId   | 1234567890987654321a                   |
      | media[0].renditions[1].name            | 4-3                                    |
      | media[0].renditions[1].image.assetId   | 1234567890987654321b                   |
      | media[0].renditions[2].name            | original                               |
      | media[0].renditions[2].image.assetId   | 1234567890987654321c                   |
      | media[1].image.assetId                 | 2234567890987654321c                   |
      | media[1].renditions[0].name            | 16-9                                   |
      | media[1].renditions[0].image.assetId   | 2234567890987654321a                   |
      | media[1].renditions[1].name            | 4-3                                    |
      | media[1].renditions[1].image.assetId   | 2234567890987654321b                   |
      | media[1].renditions[2].name            | original                               |
      | media[1].renditions[2].image.assetId   | 2234567890987654321c                   |
      | slideshows[0].code                     | slideshow1                             |
      | slideshows[0].id                       | 2                                      |
      | _links.slideshows.href                 | /api/v1/content/slideshows/6           |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/slideshows/6/2"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | code                   | slideshow1                              |
      | article.id             | 6                                       |
      | id                     | 2                                       |
      | _links.items.href      | /api/v1/content/slideshows/6/2/items/   |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/slideshows/6/2/items/"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 2
    And the JSON nodes should contain:
      | _embedded._items[0].articleMedia.image.assetId   | 1234567890987654321c |
      | _embedded._items[1].articleMedia.image.assetId   | 2234567890987654321c |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/slideshows/6/1/items/"
    Then the response status code should be 404

    And The payload received by "http://localhost:3000/article-update-check" webhook should be equal to:
    """
    {
      "id":6,
      "title":"Article with added slideshow",
      "body":"<p>some html body</p> ",
      "slug":"abstract-html-test-without-slideshow",
      "publishedAt":"2019-03-10T09:00:00+00:00",
      "status":"published",
      "route":{
        "requirements":{
          "slug":"[a-zA-Z0-9*\\-_]+"
        },
        "id":3,
        "staticPrefix":"/news",
        "variablePattern":"/{slug}",
        "children":[
          {
            "requirements":{
              "slug":"[a-zA-Z0-9*\\-_]+"
            },
            "id":6,
            "staticPrefix":"/news/sports",
            "variablePattern":"/{slug}",
            "parent":3,
            "children":[

            ],
            "lft":4,
            "rgt":5,
            "level":1,
            "type":"collection",
            "cacheTimeInSeconds":0,
            "name":"sports",
            "slug":"sports",
            "position":0,
            "articlesCount":0,
            "paywallSecured":false,
            "_links":{
              "self":{
                "href":"/api/v1/content/routes/6"
              },
              "parent":{
                "href":"/api/v1/content/routes/3"
              }
            }
          }
        ],
        "lft":3,
        "rgt":6,
        "level":0,
        "type":"collection",
        "cacheTimeInSeconds":0,
        "name":"news",
        "slug":"news",
        "position":1,
        "articlesCount":0,
        "paywallSecured":false,
        "_links":{
          "self":{
            "href":"/api/v1/content/routes/3"
          }
        }
      },
      "isPublishable":true,
      "metadata":{
        "subject":[
          {
            "name":"lawyer",
            "code":"02002001"
          }
        ],
        "urgency":3,
        "priority":6,
        "located":"Warsaw",
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
        "service":[
          {
            "name":"Australian General News",
            "code":"a"
          }
        ],
        "type":"text",
        "byline":"ADmin",
        "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf2",
        "language":"en"
      },
      "media":[
        {
          "id":"3",
          "image":{
            "id":"8",
            "fileExtension":"jpeg",
            "assetId":"1234567890987654321c"
          },
          "description":"test image",
          "byLine":"Paweł Mikołajczuk",
          "altText":"test image",
          "usageTerms":"indefinite-usage",
          "renditions":[
            {
              "width":1079,
              "height":720,
              "name":"16-9",
              "id":7,
              "image":{
                "id":"6",
                "fileExtension":"jpeg",
                "assetId":"1234567890987654321a"
              },
              "previewUrl":"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"
            },
            {
              "width":800,
              "height":533,
              "name":"4-3",
              "id":8,
              "image":{
                "id":"7",
                "fileExtension":"jpeg",
                "assetId":"1234567890987654321b"
              },
              "previewUrl":"http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"
            },
            {
              "width":4000,
              "height":2667,
              "name":"original",
              "id":9,
              "image":{
                "id":"8",
                "fileExtension":"jpeg",
                "assetId":"1234567890987654321c"
              },
              "previewUrl":"http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"
            }
          ],
          "headline":"test image",
          "_links":{
            "download":{
              "href":"/media/1234567890987654321c.jpeg"
            }
          }
        },
        {
          "id":"4",
          "image":{
            "id":"11",
            "fileExtension":"jpeg",
            "assetId":"2234567890987654321c"
          },
          "description":"test image 2",
          "byLine":"Paweł Mikołajczuk",
          "altText":"test image",
          "usageTerms":"indefinite-usage",
          "renditions":[
            {
              "width":1079,
              "height":720,
              "name":"16-9",
              "id":10,
              "image":{
                "id":"9",
                "fileExtension":"jpeg",
                "assetId":"2234567890987654321a"
              },
              "previewUrl":"http://localhost:5000/api/upload/2234567890987654321a/raw?_schema=http"
            },
            {
              "width":800,
              "height":533,
              "name":"4-3",
              "id":11,
              "image":{
                "id":"10",
                "fileExtension":"jpeg",
                "assetId":"2234567890987654321b"
              },
              "previewUrl":"http://localhost:5000/api/upload/2234567890987654321b/raw?_schema=http"
            },
            {
              "width":4000,
              "height":2667,
              "name":"original",
              "id":12,
              "image":{
                "id":"11",
                "fileExtension":"jpeg",
                "assetId":"2234567890987654321c"
              },
              "previewUrl":"http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http"
            }
          ],
          "headline":"test image",
          "_links":{
            "download":{
              "href":"/media/2234567890987654321c.jpeg"
            }
          }
        }
      ],
      "lead":"some abstract text",
      "code":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf2",
      "sources":[

      ],
      "extra":[

      ],
      "slideshows":[
        {
          "id":2,
          "code":"slideshow1",
          "items":[
            {
              "articleMedia":{
                "id":"3",
                "image":{
                  "id":"8",
                  "fileExtension":"jpeg",
                  "assetId":"1234567890987654321c"
                },
                "description":"test image",
                "byLine":"Paweł Mikołajczuk",
                "altText":"test image",
                "usageTerms":"indefinite-usage",
                "renditions":[
                  {
                    "width":1079,
                    "height":720,
                    "name":"16-9",
                    "id":7,
                    "image":{
                      "id":"6",
                      "fileExtension":"jpeg",
                      "assetId":"1234567890987654321a"
                    },
                    "previewUrl":"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"
                  },
                  {
                    "width":800,
                    "height":533,
                    "name":"4-3",
                    "id":8,
                    "image":{
                      "id":"7",
                      "fileExtension":"jpeg",
                      "assetId":"1234567890987654321b"
                    },
                    "previewUrl":"http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"
                  },
                  {
                    "width":4000,
                    "height":2667,
                    "name":"original",
                    "id":9,
                    "image":{
                      "id":"8",
                      "fileExtension":"jpeg",
                      "assetId":"1234567890987654321c"
                    },
                    "previewUrl":"http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"
                  }
                ],
                "headline":"test image",
                "_links":{
                  "download":{
                    "href":"/media/1234567890987654321c.jpeg"
                  }
                }
              }
            },
            {
              "articleMedia":{
                "id":"4",
                "image":{
                  "id":"11",
                  "fileExtension":"jpeg",
                  "assetId":"2234567890987654321c"
                },
                "description":"test image 2",
                "byLine":"Paweł Mikołajczuk",
                "altText":"test image",
                "usageTerms":"indefinite-usage",
                "renditions":[
                  {
                    "width":1079,
                    "height":720,
                    "name":"16-9",
                    "id":10,
                    "image":{
                      "id":"9",
                      "fileExtension":"jpeg",
                      "assetId":"2234567890987654321a"
                    },
                    "previewUrl":"http://localhost:5000/api/upload/2234567890987654321a/raw?_schema=http"
                  },
                  {
                    "width":800,
                    "height":533,
                    "name":"4-3",
                    "id":11,
                    "image":{
                      "id":"10",
                      "fileExtension":"jpeg",
                      "assetId":"2234567890987654321b"
                    },
                    "previewUrl":"http://localhost:5000/api/upload/2234567890987654321b/raw?_schema=http"
                  },
                  {
                    "width":4000,
                    "height":2667,
                    "name":"original",
                    "id":12,
                    "image":{
                      "id":"11",
                      "fileExtension":"jpeg",
                      "assetId":"2234567890987654321c"
                    },
                    "previewUrl":"http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http"
                  }
                ],
                "headline":"test image",
                "_links":{
                  "download":{
                    "href":"/media/2234567890987654321c.jpeg"
                  }
                }
              }
            }
          ],
          "createdAt":"2019-03-10T09:00:00+00:00",
          "updatedAt":"2019-03-10T09:00:00+00:00",
          "_links":{
            "items":{
              "href":"/api/v1/content/slideshows/6/2/items/"
            }
          }
        }
      ],
      "createdAt":"2019-03-10T09:00:00+00:00",
      "updatedAt":"2019-03-10T09:00:00+00:00",
      "authors":[

      ],
      "keywords":[
        {
          "slug":"keyword1",
          "name":"keyword1"
        },
        {
          "slug":"keyword2",
          "name":"keyword2"
        }
      ],
      "isPublishedFBIA":false,
      "articleStatistics":{
        "impressionsNumber":0,
        "pageViewsNumber":0,
        "internalClickRate":0,
        "createdAt":"2019-03-10T09:00:00+00:00",
        "updatedAt":"2019-03-10T09:00:00+00:00"
      },
      "commentsCount":0,
      "tenant":{
        "id":1,
        "domainName":"localhost",
        "code":"123abc",
        "name":"Default tenant",
        "ampEnabled":true,
        "_links":{
          "self":{
            "href":"/api/v1/tenants/123abc"
          }
        }
      },
      "paywallSecured":false,
      "contentLists":[

      ],
      "_links":{
        "self":{
          "href":"/api/v1/content/articles/abstract-html-test-without-slideshow"
        },
        "online":{
          "href":"/news/abstract-html-test-without-slideshow"
        },
        "related":{
          "href":"/api/v1/content/articles/6/related/"
        },
        "slideshows":{
          "href":"/api/v1/content/slideshows/6"
        }
      }
    }
  """
