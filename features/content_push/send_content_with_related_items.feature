@content_push
Feature: Related items support
  In order to be able to display related articles
  As a HTTP Client
  I want to able to receive and parse the request with related items payload

  Scenario: Pushing the content with related items data
    Given I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/push" with body:
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
              "slugline":"abstract-html-test-1",
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
      "pubstatus":"usable"
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
              "published":true
            }
          ]
        }
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/abstract-html-test"
    Then the response status code should be 200
#    And the JSON nodes should contain:
#      | media[0].image.assetId                 | 1234567890987654321c                   |
#      | media[0].renditions[0].name            | 16-9                                   |
#      | media[0].renditions[0].image.assetId   | 1234567890987654321a                   |
#      | media[0].renditions[1].name            | 4-3                                    |
#      | media[0].renditions[1].image.assetId   | 1234567890987654321b                   |
#      | media[0].renditions[2].name            | original                               |
#      | media[0].renditions[2].image.assetId   | 1234567890987654321c                   |
#      | media[1].image.assetId                 | 2234567890987654321c                   |
#      | media[1].renditions[0].name            | 16-9                                   |
#      | media[1].renditions[0].image.assetId   | 2234567890987654321a                   |
#      | media[1].renditions[1].name            | 4-3                                    |
#      | media[1].renditions[1].image.assetId   | 2234567890987654321b                   |
#      | media[1].renditions[2].name            | original                               |
#      | media[1].renditions[2].image.assetId   | 2234567890987654321c                   |
#      | slideshows[0].code                     | slideshow1                             |
#      | _links.related.href                 | /api/v1/content/articles/6/related/           |

#    And I am authenticated as "test.user"
#    And I add "Content-Type" header equal to "application/json"
#    Then I send a "GET" request to "/api/v1/content/slideshows/6/1"
#    Then the response status code should be 200
#    And the JSON nodes should contain:
#      | code                   | slideshow1                              |
#      | article.id             | 6                                       |
#      | id                     | 1                                       |
#      | _links.items.href      | /api/v1/content/slideshows/6/1/items/   |
#
#    And I am authenticated as "test.user"
#    And I add "Content-Type" header equal to "application/json"
#    Then I send a "GET" request to "/api/v1/content/slideshows/6/1/items/"
#    Then the response status code should be 200
#    And the JSON node "total" should be equal to 2
#    And the JSON nodes should contain:
#      | _embedded._items[0].articleMedia.image.assetId   | 1234567890987654321c |
#      | _embedded._items[1].articleMedia.image.assetId   | 2234567890987654321c |
