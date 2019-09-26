@content_push
Feature: Related items support
  In order to be able to display related articles
  As a HTTP Client
  I want to able to receive and parse the request with related items payload

  Scenario: Pushing the content with related items data
    Given I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "language":"en",
      "slugline":"abstract-html-test-1",
      "body_html":"<p>some html body related 1</p>",
      "versioncreated":"2016-09-23T13:57:28+0000",
      "firstcreated":"2016-05-25T10:23:15+0000",
      "description_text":"some abstract text related 1",
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
      "version":"2",
      "byline":"ADmin",
      "keywords":[
        "keyword1",
        "keyword2"
      ],
      "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cr9",
      "priority":6,
      "subject":[
        {
          "name":"lawyer",
          "code":"02002001"
        }
      ],
      "urgency":3,
      "type":"text",
      "headline":"hello world 1",
      "service":[
        {
          "name":"Australian General News",
          "code":"a"
        }
      ],
      "description_html":"<p><b><u>some abstract text related 1</u></b></p>",
      "located":"Warsaw",
      "pubstatus":"usable",
      "associations":{
        "featuremedia":{
          "authors":[
            {
              "role":"photographer",
              "biography":"",
              "name":"Doe"
            }
          ],
          "versioncreated":"2018-12-31T10:10:02+0000",
          "language":"en",
          "renditions":{
            "original":{
              "mimetype":"image/jpeg",
              "height":2000,
              "media":"1234567890987654321b",
              "href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http",
              "width":3000
            }
          },
          "usageterms":"",
          "copyrightnotice":"",
          "byline":"Doe",
          "copyrightholder":"",
          "type":"picture",
          "body_html":"",
          "pubstatus":"usable",
          "headline":"Sad",
          "body_text":"Sad",
          "keywords":[

          ],
          "guid":"http://localhost:3000/api/upload/20181231111216/5c29ece5d05508291620aa51jpeg.jpg",
          "version":"1",
          "priority":5,
          "description_text":"Sad"
        }
      }
    }
    """
    Then the response status code should be 201

    Given default tenant with code "123abc"
    Given the following Routes:
      |  name | type       | slug | templateName               |
      |  test | collection | test | related_articles.html.twig |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/6/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true,
              "route": 7
            }
          ]
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test-1-0123456789abc"
    Then the response status code should be 200

    Then I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
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
        "related_item1":{
          "type":"related_content",
          "items":[
            {
              "language":"en",
              "slugline":"abstract-html-test-1",
              "body_html":"<p>some html body related 1</p>",
              "versioncreated":"2016-09-23T13:57:28+0000",
              "firstcreated":"2016-05-25T10:23:15+0000",
              "description_text":"some abstract text related 1",
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
              "version":"2",
              "byline":"ADmin",
              "keywords":[
                "keyword1",
                "keyword2"
              ],
              "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cr9",
              "priority":6,
              "subject":[
                {
                  "name":"lawyer",
                  "code":"02002001"
                }
              ],
              "urgency":3,
              "type":"text",
              "headline":"hello world 1",
              "service":[
                {
                  "name":"Australian General News",
                  "code":"a"
                }
              ],
              "description_html":"<p><b><u>some abstract text related 1</u></b></p>",
              "located":"Warsaw",
              "pubstatus":"usable"
            },
            {
              "language":"en",
              "slugline":"abstract-html-test-2",
              "body_html":"<p>some html body related 1</p>",
              "versioncreated":"2016-09-23T13:57:28+0000",
              "firstcreated":"2016-05-25T10:23:15+0000",
              "description_text":"some abstract text related 2",
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
              "version":"2",
              "byline":"ADmin",
              "keywords":[
                "keyword1",
                "keyword2"
              ],
              "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3r74",
              "priority":6,
              "subject":[
                {
                  "name":"lawyer",
                  "code":"02002001"
                }
              ],
              "urgency":3,
              "type":"text",
              "headline":"hello world 2",
              "service":[
                {
                  "name":"Australian General News",
                  "code":"a"
                }
              ],
              "description_html":"<p><b><u>some abstract text related 2</u></b></p>",
              "located":"Warsaw",
              "pubstatus":"usable"
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
      "headline":"abstract html test",
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
        "featuremedia":{
          "authors":[
            {
              "role":"photographer",
              "biography":"",
              "name":"Doe"
            }
          ],
          "versioncreated":"2018-12-31T10:10:02+0000",
          "language":"en",
          "renditions":{
            "original":{
              "mimetype":"image/jpeg",
              "height":2000,
              "media":"1234567890987654321a",
              "href":"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http",
              "width":3000
            }
          },
          "usageterms":"",
          "copyrightnotice":"",
          "byline":"Doe",
          "copyrightholder":"",
          "type":"picture",
          "body_html":"",
          "pubstatus":"usable",
          "headline":"Sad",
          "body_text":"Sad",
          "keywords":[

          ],
          "guid":"http://localhost:3000/api/upload/20181231111216/5c29ece5d05508291620aa50jpeg.jpg",
          "version":"1",
          "priority":5,
          "description_text":"Sad"
        },
        "related--1":{
          "language":"en",
          "slugline":"abstract-html-test-1",
          "body_html":"<p>some html body related 1</p>",
          "versioncreated":"2016-09-23T13:57:28+0000",
          "firstcreated":"2016-05-25T10:23:15+0000",
          "description_text":"some abstract text related 1",
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
          "version":"2",
          "byline":"ADmin",
          "keywords":[
            "keyword1",
            "keyword2"
          ],
          "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cr9",
          "priority":6,
          "subject":[
            {
              "name":"lawyer",
              "code":"02002001"
            }
          ],
          "urgency":3,
          "type":"text",
          "headline":"hello world 1",
          "service":[
            {
              "name":"Australian General News",
              "code":"a"
            }
          ],
          "description_html":"<p><b><u>some abstract text related 1</u></b></p>",
          "located":"Warsaw",
          "pubstatus":"usable",
          "associations": {
            "featuremedia":{
              "authors":[
                {
                  "role":"photographer",
                  "biography":"",
                  "name":"Doe"
                }
              ],
              "versioncreated":"2018-12-31T10:10:02+0000",
              "language":"en",
              "renditions":{
                "original":{
                  "mimetype":"image/jpeg",
                  "height":2000,
                  "media":"1234567890987654321b",
                  "href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http",
                  "width":3000
                }
              },
              "usageterms":"",
              "copyrightnotice":"",
              "byline":"Doe",
              "copyrightholder":"",
              "type":"picture",
              "body_html":"",
              "pubstatus":"usable",
              "headline":"Sad",
              "body_text":"Sad",
              "keywords":[

              ],
              "guid":"http://localhost:3000/api/upload/20181231111216/5c29ece5d05508291620aa51jpeg.jpg",
              "version":"1",
              "priority":5,
              "description_text":"Sad"
            }
          }
        },
        "related--2":{
          "language":"en",
          "slugline":"abstract-html-test-2",
          "body_html":"<p>some html body related 1</p>",
          "versioncreated":"2016-09-23T13:57:28+0000",
          "firstcreated":"2016-05-25T10:23:15+0000",
          "description_text":"some abstract text related 2",
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
          "version":"2",
          "byline":"ADmin",
          "keywords":[
            "keyword1",
            "keyword2"
          ],
          "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3r74",
          "priority":6,
          "subject":[
            {
              "name":"lawyer",
              "code":"02002001"
            }
          ],
          "urgency":3,
          "type":"text",
          "headline":"hello world 2",
          "service":[
            {
              "name":"Australian General News",
              "code":"a"
            }
          ],
          "description_html":"<p><b><u>some abstract text related 2</u></b></p>",
          "located":"Warsaw",
          "pubstatus":"usable"
        }
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/7/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true,
              "route": 7
            }
          ]
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the JSON node "body" should be equal to "<p>some html body</p>"
    And the JSON node "feature_media.image.asset_id" should be equal to "1234567890987654321a"
    And the JSON node "feature_media._links.download.href" should be equal to "/media/1234567890987654321a.png"
    And the JSON nodes should contain:
      | _links.related.href  | /api/v2/content/articles/7/related/  |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/7/related/"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 1
    And the JSON node "_embedded._items[0].id" should not exist
    And the JSON node "_embedded._items[0].relates_to" should not exist
    And the JSON node "_embedded._items[0].article.title" should be equal to "hello world 1"
    And the JSON node "_embedded._items[0].article.id" should be equal to 6
    And the JSON node "_embedded._items[0].updated_at" should exist
    And the JSON node "_embedded._items[0].created_at" should exist
    And the JSON node "_embedded._items[0].article.feature_media.image.asset_id" should be equal to "1234567890987654321b"

    When I go to "/test/abstract-html-test"
    Then the response status code should be 200
    And the response should contain "http://localhost/uploads/swp/123456/media/1234567890987654321a.png"
    And the response should contain "some html body"
    And the response should contain "hello world 1"
    And the response should contain "http://localhost/uploads/swp/123456/media/1234567890987654321b.png"

    Then I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
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
      "extra_items":{},
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
      "headline":"abstract html test",
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
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/7/related/"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 0
