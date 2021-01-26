@articles
Feature: Set alt text fallback value based on the value from headline field.

  Scenario: Alt text fallback
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given the following Package ninjs:
    """
    {
      "located":"Warsaw",
      "profile":"583d545634d0c100405d84d2",
      "version":"3",
      "type":"text",
      "slugline":"item test",
      "priority":6,
      "description_html":"<p>abstract</p>",
      "guid":"urn:newsml:localhost:2017-02-07T07:46:48.027116:2cde1d3f-302f-4cf9-a4b9-809d2320cc00",
      "pubstatus":"usable",
      "associations":{
        "embedded9582903151":{
          "version":"4",
          "type":"picture",
          "priority":6,
          "renditions":{
            "viewImage":{
              "width":640,
              "mimetype":"image/png",
              "poi":{
                "x":339,
                "y":115
              },
              "media":"20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg",
              "height":426,
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw"
            },
            "thumbnail":{
              "width":180,
              "mimetype":"image/png",
              "poi":{
                "x":95,
                "y":32
              },
              "media":"20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg",
              "height":120,
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw"
            },
            "original":{
              "width":2048,
              "mimetype":"image/jpeg",
              "poi":{
                "x":1085,
                "y":368
              },
              "media":"20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg",
              "height":1365,
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw"
            }
          },
          "pubstatus":"usable",
          "place":[

          ],
          "firstcreated":"2016-12-06T16:59:49+0000",
          "mimetype":"image/jpeg",
          "body_text":"",
          "description_text":"Few of a kind",
          "urgency":3,
          "language":"en",
          "headline":"Bell Peppers",
          "guid":"tag:localhost:2016:a5199d69-1dce-4572-bb1a-34ed2953ea72",
          "byline":"Ljub. Z. Rankovi\u0107",
          "versioncreated":"2016-12-06T17:13:18+0000"
        }
      },
      "place":[

      ],
      "firstcreated":"2017-02-07T07:46:48+0000",
      "body_html":"<p>some text and after that we should get image</p>\n<!-- EMBED START Image {id: \"embedded9582903151\"} -->\n<figure><img src=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg\" alt=\"\" srcset=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg 450w, https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg 777w\" /><figcaption>Few of a kind</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded9582903151\"} -->\n<p>and after image again some text</p><p>footer content</p>",
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
    Then I send a "GET" request to "/api/v2/content/articles/item-test"
    Then the response status code should be 200
    And the JSON node "body" should contain "Bell Peppers"
