@disable-fixtures
Feature: Replace the embedded images when there are new lines in the body

  Scenario: Replace the embedded images
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default | code   | themeName      |
      | Default      | test | 123abc |           | localhost   | true    | true    | 123abc | swp/test-theme |

    Given the following Users:
      | username   | email                      | token      | password | role                |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   |

    Given the following organization publishing rule:
    """
    {
        "name":"Test rule",
        "description":"Test rule description",
        "priority":1,
        "expression":"true == true",
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
    """

    Given the following Routes:
      |  name      | type       | slug     |
      |  Tech News | collection | technews |

    Given the following tenant publishing rule:
    """
      {
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"true == true",
          "configuration":[
            {
              "key":"route",
              "value":1
            },
            {
              "key":"published",
              "value":true
            }
          ]
      }
    """

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "version":"4",
      "copyrightholder":"",
      "extra":{
        "overtitle":"test"
      },
      "extra_items":{
      },
      "source":"test",
      "copyrightnotice":"",
      "associations":{
        "editor_1":{
          "version":"2",
          "copyrightholder":"test",
          "byline":"test",
          "source":"",
          "headline":"test",
          "language":"en",
          "firstcreated":"2018-12-18T11:23:31+0000",
          "copyrightnotice":"test",
          "mimetype":"image/jpeg",
          "guid":"tag:2018:1f3a8e88-4a90-4aea-93da-b2e54b2210c3",
          "body_text":"dfsdf",
          "urgency":3,
          "type":"picture",
          "versioncreated":"2018-12-18T11:23:34+0000",
          "priority":6,
          "genre":[
            {
              "code":"Article",
              "name":"Article (news)"
            }
          ],
          "description_text":"Fig 2",
          "pubstatus":"usable",
          "renditions":{
            "original":{
              "href":"http://localhost:3000/api/upload/20181218121220_7744cc65a42961a502572e5220c548910355784cfcd48caf26e7e0a9621b4dd5/raw?_schema=http",
              "height":960,
              "width":1280,
              "media":"20181218121220/7744cc65a42961a502572e5220c548910355784cfcd48caf26e7e0a9621b4dd5.jpg",
              "mimetype":"image/jpeg",
              "poi":{
                "x":640,
                "y":480
              }
            }
          }
        },
        "featuremedia":{
          "version":"2",
          "copyrightholder":"test",
          "byline":"test",
          "source":"",
          "headline":"test",
          "language":"en",
          "firstcreated":"2018-12-18T11:21:15+0000",
          "copyrightnotice":"test",
          "mimetype":"image/jpeg",
          "guid":"tag:2018:d2da4b43-0f57-4aaa-9f80-4fa936bf5895",
          "body_text":"gggg",
          "urgency":3,
          "type":"picture",
          "versioncreated":"2018-12-18T11:21:17+0000",
          "priority":6,
          "genre":[
            {
              "code":"Article",
              "name":"Article (news)"
            }
          ],
          "description_text":"Fig",
          "pubstatus":"usable",
          "renditions":{
            "original":{
              "href":"http://localhost:3000/api/upload/20181218121220_be9e7160c42fd3c24a8e647acb227d669d0ac6e68a26e7f14d70f9ec6c4252ea.jpg/raw?_schema=http",
              "height":960,
              "width":1280,
              "media":"20181218121220/be9e7160c42fd3c24a8e647acb227d669d0ac6e68a26e7f14d70f9ec6c4252ea.jpg",
              "mimetype":"image/jpeg",
              "poi":{
                "x":691,
                "y":96
              }
            }
          }
        },
        "editor_0":{
          "version":"2",
          "copyrightholder":"test",
          "byline":"test",
          "source":"",
          "headline":"test",
          "language":"en",
          "firstcreated":"2018-12-18T11:22:39+0000",
          "copyrightnotice":"test",
          "mimetype":"image/jpeg",
          "guid":"tag:2018:e1b27120-5738-4b4f-ae67-6ac0432a7627",
          "body_text":"jhjhj",
          "urgency":3,
          "type":"picture",
          "versioncreated":"2018-12-18T11:22:41+0000",
          "priority":6,
          "genre":[
            {
              "code":"Article",
              "name":"Article (news)"
            }
          ],
          "description_text":"Fig",
          "pubstatus":"usable",
          "renditions":{
            "original":{
              "href":"http://localhost:3000/api/upload/20181218121220_a3c082eea33af42478d4fe57b2ae97702b378faa481e5825028f821ee37d2d62/raw?_schema=http",
              "height":1280,
              "width":960,
              "media":"20181218121220/a3c082eea33af42478d4fe57b2ae97702b378faa481e5825028f821ee37d2d62.jpg",
              "mimetype":"image/jpeg",
              "poi":{
                "x":480,
                "y":640
              }
            }
          }
        }
      },
      "keywords":[
        "Josip Broz Tito",
        "Podgorica"
      ],
      "versioncreated":"2019-05-17T09:24:44+0000",
      "evolvedfrom":"urn:newsml:2018-12-18T12:00:28.426605:39c14b99-f32b-4f89-874a-73be793e2fd9",
      "annotations":[

      ],
      "priority":5,
      "profile":"News",
      "authors":[
        {
          "role":"writer",
          "name":"Doe",
          "biography":""
        }
      ],
      "guid":"urn:newsml:2018-12-18T14:14:04.591619:3ff2e5e2-d5b1-467f-8d2f-241b0deed45e",
      "pubstatus":"usable",
      "headline":"Lorem",
      "language":"en",
      "wordcount":34,
      "firstcreated":"2018-12-18T13:14:04+0000",
      "usageterms":"",
      "type":"text",
      "readtime":0,
      "firstpublished":"2019-05-17T09:24:43+0000",
      "charcount":257,
      "body_html":"<p>Line 1.</p>\n<!-- EMBED START Image {id: \"editor_0\"} -->\n<figure>\n <img src=\"http://localhost:3000/api/upload/20181218121220_a3c082eea33af42478d4fe57b2ae97702b378faa481e5825028f821ee37d2d62/raw?_schema=http\" alt=\"jhjhj\" />\n <figcatpion>Fig</figcaption>\n</figure>\n<!-- EMBED END Image {id: \"editor_0\"} -->\n<p>Line</p>\n<!-- EMBED START Image {id: \"editor_1\"} -->\n<figure>\n <img src=\"http://localhost:3000/api/upload/20181218121220_7744cc65a42961a502572e5220c548910355784cfcd48caf26e7e0a9621b4dd5/raw?_schema=http\" alt=\"dfsdf\" />\n <figcatpion>Fig 2</figcaption>\n</figure>\n<!-- EMBED END Image {id: \"editor_1\"} -->\n",
      "service":[
        {
          "code":"test",
          "name":"Test"
        }
      ]
    }
    """
    Then the response status code should be 201

    When I go to "http://localhost/technews/lorem"
    Then the response status code should be 200
    And the response should contain "editor_0"
    And the response should contain "/media/20181218121220_a3c082eea33af42478d4fe57b2ae97702b378faa481e5825028f821ee37d2d62.png"
    And the response should contain "editor_1"
    And the response should contain "/media/20181218121220_7744cc65a42961a502572e5220c548910355784cfcd48caf26e7e0a9621b4dd5.png"
