@routes
@disable-fixtures
Feature: Delete route with token preview reference
  In order to not break the API
  As a HTTP Client
  I want to be able to delete token preview entity referencing the deleted route

  Scenario: Change route template
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      | code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc |

    Given the following Routes:
      |  name | type       | slug | templateName       |
      |  test | collection | test | seo_twig.html.twig |

    Given the following Articles:
      | title               | route      | status    | isPublishable  |
      | Lorem               | test       | published | true           |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/preview/package/generate_token/1" with body:
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
        "embedded9582903151":{
          "version":"4",
          "type":"picture",
          "priority":6,
          "renditions":{
            "viewImage":{
              "width":640,
              "mimetype":"image/jpeg",
              "poi":{
                "x":339,
                "y":115
              },
              "media":"20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg",
              "height":426,
              "href":"https://amazonaws.com/20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg"
            },
            "thumbnail":{
              "width":180,
              "mimetype":"image/jpeg",
              "poi":{
                "x":95,
                "y":32
              },
              "media":"20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg",
              "height":120,
              "href":"https://amazonaws.com/20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg"
            },
            "baseImage":{
              "width":1400,
              "mimetype":"image/jpeg",
              "poi":{
                "x":742,
                "y":251
              },
              "media":"20161206161256/876f2496c559df9a1a2ff37a90e9e14cba7d0f210082181f5d69149504fe7773.jpg",
              "height":933,
              "href":"https://amazonaws.com/20161206161256/876f2496c559df9a1a2ff37a90e9e14cba7d0f210082181f5d69149504fe7773.jpg"
            },
            "original":{
              "width":2048,
              "mimetype":"image/jpeg",
              "poi":{
                "x":1085,
                "y":368
              },
              "media":"20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg",
              "height":1365,
              "href":"https://amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg"
            },
            "600x300":{
              "width":450,
              "mimetype":"image/jpeg",
              "poi":{
                "x":1085,
                "y":368
              },
              "media":"20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg",
              "height":300,
              "href":"https://amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg"
            },
            "777x600":{
              "width":777,
              "mimetype":"image/jpeg",
              "poi":{
                "x":880,
                "y":368
              },
              "media":"20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg",
              "height":517,
              "href":"https://amazonaws.com/20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg"
            }
          },
          "pubstatus":"usable",
          "place":[

          ],
          "firstcreated":"2016-12-06T16:59:49+0000",
          "mimetype":"image/jpeg",
          "body_text":"Bell Peppers",
          "description_text":"Few of a kind",
          "urgency":3,
          "language":"en",
          "headline":"Bell Peppers",
          "guid":"tag:localhost:2016:a5199d69-1dce-4572-bb1a-34ed2953ea72",
          "byline":"Ljub. Z. Rankovi\u0107",
          "versioncreated":"2016-12-06T17:13:18+0000"
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
            "viewImage":{
              "width":640,
              "mimetype":"image/jpeg",
              "poi":{
                "x":384,
                "y":183
              },
              "media":"20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg",
              "height":426,
              "href":"https://amazonaws.com/20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg"
            },
            "thumbnail":{
              "width":180,
              "mimetype":"image/jpeg",
              "poi":{
                "x":108,
                "y":51
              },
              "media":"20170111140132/e6fd5d3ed6cff77e2556fde13bf9f383a33795760c42e693a40d0479794febf4.jpg",
              "height":120,
              "href":"https://amazonaws.com/20170111140132/e6fd5d3ed6cff77e2556fde13bf9f383a33795760c42e693a40d0479794febf4.jpg"
            },
            "baseImage":{
              "width":1400,
              "mimetype":"image/jpeg",
              "poi":{
                "x":840,
                "y":401
              },
              "media":"20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg",
              "height":933,
              "href":"https://amazonaws.com/20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg"
            },
            "original":{
              "width":2048,
              "mimetype":"image/jpeg",
              "poi":{
                "x":1228,
                "y":586
              },
              "media":"20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg",
              "height":1365,
              "href":"https://amazonaws.com/20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg"
            },
            "600x300":{
              "poi":{
                "x":573,
                "y":371
              },
              "width":598,
              "media":"sdsite/20170207070248/206d13a7-42aa-4618-b534-d56468a03e15.jpg",
              "height":300,
              "href":"https://amazonaws.com/sdsite/20170207070248/206d13a7-42aa-4618-b534-d56468a03e15.jpg"
            },
            "777x600":{
              "poi":{
                "x":1184,
                "y":586
              },
              "width":668,
              "media":"sdsite/20170207070248/49521a16-9b51-4ddd-a01a-59aa4db3e71f.jpg",
              "height":517,
              "href":"https://amazonaws.com/sdsite/20170207070248/49521a16-9b51-4ddd-a01a-59aa4db3e71f.jpg"
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
          "body_text":"Gradac",
          "version":"5"
        }
      },
      "place":[

      ],
      "firstcreated":"2017-02-07T07:46:48+0000",
      "body_html":"<p>some text and after that we should get image</p>\n<!-- EMBED START Image {id: \"embedded9582903151\"} -->\n<figure><img src=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg\" alt=\"Bell Peppers\" srcset=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg 450w, https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg 777w\" /><figcaption>Few of a kind</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded9582903151\"} -->\n<p>and after image again some text</p><p>footer content</p>",
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
    Then the response status code should be 200

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "DELETE" request to "/api/v2/content/routes/1"
    Then the response status code should be 204

    When I go to "http://localhost/preview/publish/package/0123456789abc"
    Then the response status code should be 404
