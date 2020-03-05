@targeted_publishing
Feature: Updating existing article.

  Scenario: Make sure the feature media is not deleted when article is updated.
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"123abc",
          "route":6,
          "isPublishedFbia":false,
          "published":true,
          "packageGuid": "urn:newsml:localhost:2019-08-14T11:14:54.501451:794e7c20-1013-47ec-bb25-3f398315d104",
          "paywallSecured":false
      }
    """
    Then the response status code should be 200

    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "body_html":"<p><b>feature media test rafal 2</b></p>\n<p><b>feature media test rafal 2</b></p>\n<p><b>test</b></p>\n<p><b>sssss</b></p>",
      "description_html":"<p>lorem ipsum</p>",
      "readtime":0,
      "firstcreated":"2019-08-14T11:14:54+0000",
      "charcount":61,
      "annotations":[

      ],
      "authors":[
        {
          "name":"Admin Istrator",
          "role":"writer",
          "biography":""
        }
      ],
      "keywords":[
        "renditions"
      ],
      "guid":"urn:newsml:localhost:2019-08-14T11:14:54.501451:794e7c20-1013-47ec-bb25-3f398315d104",
      "pubstatus":"usable",
      "wordcount":12,
      "extra":{
        "seo_title":"feature media test rafal 2"
      },
      "priority":5,
      "firstpublished":"2019-08-14T12:01:21+0000",
      "type":"text",
      "source":"vrt3",
      "headline":"feature media test rafal 2",
      "version":"3",
      "usageterms":"",
      "copyrightnotice":"",
      "description_text":"lorem ipsum",
      "language":"en",
      "copyrightholder":"",
      "profile":"verticals_news_article",
      "versioncreated":"2019-08-14T12:01:21+0000",
      "associations":{
        "featuremedia":{
          "renditions":{
            "original":{
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw",
              "poi":{
                "y":1984,
                "x":1488
              },
              "mimetype":"image/png",
              "width":2976,
              "height":3968,
              "media":"5d53ed7f59fd39020063e6f1"
            }
          },
          "headline":"bialowieza",
          "firstcreated":"2019-08-14T11:16:15+0000",
          "urgency":3,
          "version":"2",
          "usageterms":"",
          "copyrightnotice":"",
          "guid":"tag:localhost:2019:5dca5dcb-3611-4b86-b13b-fb807d4462c5",
          "pubstatus":"usable",
          "body_text":"bialowieza",
          "language":"en",
          "description_text":"bialowieza",
          "priority":6,
          "copyrightholder":"",
          "mimetype":"image/png",
          "genre":[
            {
              "name":"Article (news)",
              "code":"Article"
            }
          ],
          "versioncreated":"2019-08-14T11:16:16+0000",
          "type":"picture",
          "source":"Superdesk"
        }
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | slug                          | feature-media-test-rafal-2 |
      | route.id                      | 6                        |
      | status                        | published                |
      | feature_media.id              | 1                        |

    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "body_html":"<p><b>feature media test rafal 2</b></p>\n<p><b>feature media test rafal 2</b></p>\n<p><b>test</b></p>\n<p><b>sssss</b></p>",
      "description_html":"<p>lorem ipsum</p>",
      "readtime":0,
      "firstcreated":"2019-08-14T12:05:34+0000",
      "charcount":61,
      "authors":[
        {
          "name":"Admin Istrator",
          "role":"writer",
          "biography":""
        }
      ],
      "keywords":[
        "renditions"
      ],
      "guid":"urn:newsml:localhost:2019-08-14T12:05:34.702993:13826222-9e1c-486f-967c-4bc65067e526",
      "pubstatus":"usable",
      "wordcount":12,
      "extra":{
        "seo_title":"feature media test rafal 2"
      },
      "priority":5,
      "firstpublished":"2019-08-14T12:05:51+0000",
      "type":"text",
      "source":"vrt3",
      "headline":"feature media test rafal 2",
      "version":"2",
      "usageterms":"",
      "copyrightnotice":"",
      "description_text":"lorem ipsum",
      "language":"en",
      "copyrightholder":"",
      "profile":"verticals_news_article",
      "versioncreated":"2019-08-14T12:05:51+0000",
      "evolvedfrom":"urn:newsml:localhost:2019-08-14T11:14:54.501451:794e7c20-1013-47ec-bb25-3f398315d104",
      "associations":{
        "featuremedia":{
          "renditions":{
            "original":{
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw",
              "poi":{
                "y":1984,
                "x":1488
              },
              "mimetype":"image/png",
              "width":2976,
              "height":3968,
              "media":"5d53ed7f59fd39020063e6f1"
            }
          },
          "headline":"bialowieza",
          "firstcreated":"2019-08-14T11:16:15+0000",
          "urgency":3,
          "version":"2",
          "usageterms":"",
          "copyrightnotice":"",
          "guid":"tag:localhost:2019:5dca5dcb-3611-4b86-b13b-fb807d4462c5",
          "pubstatus":"usable",
          "body_text":"bialowieza",
          "language":"en",
          "description_text":"bialowieza",
          "priority":6,
          "copyrightholder":"",
          "mimetype":"image/png",
          "genre":[
            {
              "name":"Article (news)",
              "code":"Article"
            }
          ],
          "versioncreated":"2019-08-14T11:16:16+0000",
          "type":"picture",
          "source":"Superdesk"
        }
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/6"
    Then the response status code should be 200
    And the JSON node "feature_media" should not be null
    And the JSON nodes should contain:
      | slug                          | feature-media-test-rafal-2-0123456789abc |
      | route.id                      | 6                                        |
      | status                        | published                                |
      | feature_media.id              | 3                                        |
      | feature_media.image.asset_id  | 5d53ed7f59fd39020063e6f1                 |
