@targeted_publishing
Feature: Correcting existing article.

  Scenario: Make sure the feature media is not deleted when article is corrected.
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"123abc",
          "route":6,
          "isPublishedFbia":false,
          "published":true,
          "packageGuid": "urn:newsml:localhost:2019-08-14T12:20:11.255319:877237b8-0983-4318-b28f-f919c6691bf1",
          "paywallSecured":false
      }
    """
    Then the response status code should be 200

    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "body_html":"<p><b>rafal testing dont touch</b></p>\n<p><br/></p>\n<p><b>rafal testing dont touch</b></p>\n<p><b>rafal testing dont touch</b></p>",
      "description_html":"<p><b>rafal testing dont touch</b></p>",
      "readtime":0,
      "firstcreated":"2019-08-14T12:20:11+0000",
      "charcount":72,
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
        "rend"
      ],
      "guid":"urn:newsml:localhost:2019-08-14T12:20:11.255319:877237b8-0983-4318-b28f-f919c6691bf1",
      "pubstatus":"usable",
      "wordcount":12,
      "extra":{
        "seo_title":"rafal testing dont touch"
      },
      "priority":5,
      "firstpublished":"2019-08-14T12:23:59+0000",
      "type":"text",
      "source":"vrt",
      "headline":"rafal testing dont touch",
      "version":"5",
      "usageterms":"",
      "copyrightnotice":"",
      "description_text":"rafal testing dont touch",
      "language":"en",
      "copyrightholder":"",
      "profile":"verticals_news_article",
      "versioncreated":"2019-08-14T12:23:59+0000",
      "associations":{
        "featuremedia":{
          "renditions":{
            "original":{
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw",
              "poi":{
                "y":255,
                "x":76
              },
              "mimetype":"image/png",
              "width":255,
              "height":76,
              "media":"5d53fcc00ce79a4a20508a88"
            }
          },
          "headline":"bialowieza",
          "firstcreated":"2019-08-14T12:21:20+0000",
          "urgency":3,
          "version":"2",
          "usageterms":"",
          "copyrightnotice":"",
          "guid":"tag:localhost:2019:fc8e7543-e30b-49b9-9419-2e3745b46500",
          "pubstatus":"usable",
          "body_text":"bialowieza",
          "language":"en",
          "description_text":"cofbialowieza",
          "priority":6,
          "copyrightholder":"",
          "mimetype":"image/png",
          "genre":[
            {
              "name":"Article (news)",
              "code":"Article"
            }
          ],
          "versioncreated":"2019-08-14T12:21:21+0000",
          "type":"picture",
          "source":"Superdesk"
        }
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/rafal-testing-dont-touch"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | slug                          | rafal-testing-dont-touch |
      | route.id                      | 6                        |
      | status                        | published                |
      | feature_media.id              | 1                        |
      | feature_media.image.asset_id  | 5d53fcc00ce79a4a20508a88 |

    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "body_html":"<p><b>rafal testing dont touch</b></p>\n<p><br/></p>\n<p><b>rafal testing dont touch</b></p>\n<p><b>rafal testing dont touch</b></p>",
      "description_html":"<p><b>rafal testing dont touch</b></p>",
      "readtime":0,
      "firstcreated":"2019-08-14T12:20:11+0000",
      "charcount":72,
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
        "rend"
      ],
      "guid":"urn:newsml:localhost:2019-08-14T12:20:11.255319:877237b8-0983-4318-b28f-f919c6691bf1",
      "pubstatus":"usable",
      "wordcount":12,
      "extra":{
        "seo_title":"rafal testing dont touch"
      },
      "priority":5,
      "firstpublished":"2019-08-14T12:23:59+0000",
      "type":"text",
      "source":"vrt",
      "headline":"rafal testing dont touch",
      "version":"6",
      "usageterms":"",
      "copyrightnotice":"",
      "description_text":"rafal testing dont touch",
      "language":"en",
      "copyrightholder":"",
      "profile":"verticals_news_article",
      "versioncreated":"2019-08-14T12:35:22+0000",
      "associations":{
        "featuremedia":{
          "renditions":{
            "original":{
              "href":"http://localhost:3000/api/upload/2234567890987654321a/raw",
              "poi":{
                "y":255,
                "x":76
              },
              "mimetype":"image/png",
              "width":255,
              "height":76,
              "media":"5d53fcc00ce79a4a20508a88"
            }
          },
          "headline":"bialowieza",
          "firstcreated":"2019-08-14T12:21:20+0000",
          "urgency":3,
          "version":"2",
          "usageterms":"",
          "copyrightnotice":"",
          "guid":"tag:localhost:2019:fc8e7543-e30b-49b9-9419-2e3745b46500",
          "pubstatus":"usable",
          "body_text":"bialowieza",
          "language":"en",
          "description_text":"cofbialowieza",
          "priority":6,
          "copyrightholder":"",
          "mimetype":"image/png",
          "genre":[
            {
              "name":"Article (news)",
              "code":"Article"
            }
          ],
          "versioncreated":"2019-08-14T12:21:21+0000",
          "type":"picture",
          "source":"Superdesk"
        }
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/rafal-testing-dont-touch"
    And the JSON nodes should contain:
      | slug                          | rafal-testing-dont-touch |
      | route.id                      | 6                        |
      | status                        | published                |
      | feature_media.id              | 3                        |
      | feature_media.image.asset_id  | 5d53fcc00ce79a4a20508a88 |
