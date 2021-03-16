@disable-fixtures
Feature: Public Search API endpoint

  Scenario: Return data from public search API enpoint
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following Routes:
      | name      | type       | slug     |
      | Tech News | collection | technews |

    And the current date time is "2021-03-15T09:46:13+00:00"

    Given the following Package ninjs:
    """
    {
      "located":"Warsaw",
      "profile":"583d545634d0c100405d84d2",
      "version":"3",
      "type":"text",
      "slugline":"feature media item",
      "priority":6,
      "description_html":"<p>abstract</p>",
      "guid":"urn:newsml:localhost:2017-02-07T07:46:48.027116:2cde1d3f-302f-4cf9-a4b9-809d2320cc00",
      "pubstatus":"usable",
      "associations":{
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
          "description_text":"Smoke on the water on River Gradac",
          "guid":"tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09",
          "body_text":"Gradac alt text",
          "version":"5",
          "copyrightnotice": "Notice",
          "copyrightholder": "Holder"
        }
      },
      "place":[

      ],
      "firstcreated":"2017-02-07T07:46:48+0000",
      "body_html":"<p>some text and</p><p>footer content</p>",
      "service":[
        {
          "code":"news",
          "name":"News"
        }
      ],
      "authors":[
        {
          "name":"Admin Person",
          "role":"featured",
          "biography":"",
          "avatar_url":"http://localhost:3000/api/upload/1234567890987654321b.jpg"
        }
      ],
      "description_text":"abstract",
      "urgency":3,
      "language":"en",
      "headline":"headline",
      "byline":"ADmin",
      "versioncreated":"2017-02-07T07:49:48+0000"
    }
    """

    And I publish the submitted package "urn:newsml:localhost:2017-02-07T07:46:48.027116:2cde1d3f-302f-4cf9-a4b9-809d2320cc00":
    """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true,
              "route": 1
            }
          ]
      }
    """

    When I send a GET request to "/api/v2/search/articles/"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    {
       "page":1,
       "limit":10,
       "pages":1,
       "total":5,
       "_links":{
          "self":{
             "href":"/api/v2/search/articles/?page=1&limit=10"
          },
          "first":{
             "href":"/api/v2/search/articles/?page=1&limit=10"
          },
          "last":{
             "href":"/api/v2/search/articles/?page=1&limit=10"
          }
       },
       "_embedded":{
          "_items":[
             {
                "title":"headline",
                "body":"<p>some text and</p><p>footer content</p>",
                "slug":"feature-media-item",
                "published_at":"2021-03-15T09:46:13+00:00",
                "route":{
                   "name":"Tech News",
                   "slug":"technews"
                },
                "lead":"abstract",
                "updated_at":"2021-03-15T09:46:13+00:00",
                "authors":[
                   {
                      "name":"Admin Person",
                      "role":"featured",
                      "slug":"admin-person",
                      "avatar_url":"http://localhost/author/media/1234567890987654321b.jpg"
                   }
                ],
                "feature_media":{
                   "description":"Smoke on the water on River Gradac",
                   "by_line":"Ljub. Z. Ranković",
                   "alt_text":"Gradac alt text",
                   "usage_terms":"indefinite-usage",
                   "headline":"Smoke on the water",
                   "copyright_holder":"Holder",
                   "copyright_notice":"Notice",
                   "license":{

                   },
                   "_links":{
                      "download":{
                         "href":"/media/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
                      }
                   }
                }
             }
          ]
       }
    }
    """
