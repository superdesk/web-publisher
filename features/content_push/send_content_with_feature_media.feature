@preview
@disable-fixtures
Feature: Check if the featuremedia metadata are set properly

  Scenario: Preview article with media
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

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
          "description_text":"Smoke on the water on River Gradac\u00a0",
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
              "published":true
            }
          ]
      }
     """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/feature-media-item"
    And the JSON node "feature_media.alt_text" should be equal to "Gradac alt text"
    And the JSON node "feature_media.description" should contain "Smoke on the water on River Gradac"
    And the JSON node "feature_media.usage_terms" should be equal to "indefinite-usage"
    And the JSON node "feature_media.headline" should be equal to "Smoke on the water"
    And the JSON node "feature_media.copyright_notice" should be equal to "Notice"
    And the JSON node "feature_media.copyright_holder" should be equal to "Holder"

    And the JSON node "feature_media.renditions[1].name" should be equal to "thumbnail"
    And the JSON node "feature_media.renditions[2].name" should be equal to "viewImage"
