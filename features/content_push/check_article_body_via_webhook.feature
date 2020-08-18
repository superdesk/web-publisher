@content_push
Feature: Making sure that the "body" HTML tag is not added to article's body
  In order to be able to display the article's body formatted properly in the 3rd party app
  As a HTTP Client
  I want to able to send and receive the article's data via webhook formatted properly

  Scenario: Send content with embedded image in media-block format
    Given I am authenticated as "test.user"
    And the current date time is "2019-03-10 09:00"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/webhooks/" with body:
     """
      {
          "url": "http://localhost:3000/article-update",
          "events": [
              "article[created]"
          ],
          "enabled": "1"
      }
    """
    Then  the response status code should be 201

    Given I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                                                                                |
      | media_id     | 20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg |
      | media        | @image.jpg                                                                           |
    Then the response status code should be 201

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "version":"3",
      "slugline":"test image in body",
      "copyrightholder":"",
      "copyrightnotice":"",
      "service":[
        {
          "code":"b",
          "name":"Business"
        }
      ],
      "language":"en",
      "byline":"test embedded image in body",
      "associations":{
        "editor_0":{
          "renditions":{
            "original":{
              "width":2048,
              "mimetype":"image/jpeg",
              "poi":{
                "x":1085,
                "y":368
              },
              "media":"20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg",
              "height":1365,
              "href":"https://amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg"
            }
          },
          "version":"2",
          "guid":"tag:spsd.pro:2018:626bee3c-7e5d-4e59-b860-e3d13c992b86",
          "copyrightholder":"",
          "copyrightnotice":"",
          "usageterms":"",
          "description_text":"inline image",
          "urgency":3,
          "genre":[
            {
              "code":"Article",
              "name":"Article (news)"
            }
          ],
          "body_text":"inline image",
          "firstcreated":"2018-09-04T11:33:00+0000",
          "pubstatus":"usable",
          "priority":6,
          "headline":"inline image",
          "language":"en",
          "mimetype":"image/jpeg",
          "versioncreated":"2018-09-04T11:33:01+0000",
          "type":"picture"
        }
      },
      "profile":"news",
      "description_html":"<p>test image in body</p>",
      "pubstatus":"usable",
      "firstcreated":"2018-09-04T11:30:12+0000",
      "headline":"test image in body",
      "body_html":"<p>test image in body</p><div class=\"media-block\"><img src=\"https://amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg\" alt=\"Review Bombing\" /><span class=\"media-block__description\">Review Bombing</span></div><p>new line</p>",
      "priority":5,
      "type":"text",
      "description_text":"test image in body",
      "usageterms":"",
      "guid":"urn:newsml:spsd.pro:2018-09-04T13:30:12.108807:89f823eb-6a6f-4ec9-ac09-06f5b95695d6",
      "authors":[
        {
          "name":"Admin Person",
          "role":"featured",
          "biography":""
        }
      ],
      "versioncreated":"2018-09-04T11:36:50+0000",
      "wordcount":8,
      "charcount":233,
      "readtime":0,
      "annotations":[

      ],
      "firstpublished":"2018-09-04T11:36:50+0000"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/6/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true,
              "route": 3
            }
          ]
      }
     """
    Then the response status code should be 201

    And The payload received by "http://localhost:3000/article-update-check" webhook should be equal to:
    """
    {
       "id":6,
       "title":"test image in body",
       "body":"<p>test image in body<\/p><!-- EMBED START Image {id: \"editor_0\"} --><figure><img src=\"\/uploads\/swp\/123456\/media\/20161206161256_383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpeg\" data-media-id=\"editor_0\" data-image-id=\"20161206161256_383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a\" data-rendition-name=\"original\" width=\"2048\" height=\"1365\" loading=\"lazy\" alt=\"Review Bombing\"><figcaption>Review Bombing<span><\/span><span><\/span><\/figcaption><\/figure><!-- EMBED END Image {id: \"editor_0\"} --><p>new line<\/p>",
       "slug":"test-image-in-body",
       "status":"new",
       "route":{
          "requirements":{
             "slug":"[a-zA-Z0-9*\\-_]+"
          },
          "id":3,
          "static_prefix":"\/news",
          "variable_pattern":"\/{slug}",
          "children":[
             {
                "requirements":{
                   "slug":"[a-zA-Z0-9*\\-_]+"
                },
                "id":6,
                "static_prefix":"\/news\/sports",
                "variable_pattern":"\/{slug}",
                "parent":3,
                "children":[

                ],
                "lft":4,
                "rgt":5,
                "level":1,
                "type":"collection",
                "cache_time_in_seconds":0,
                "name":"sports",
                "slug":"sports",
                "position":0,
                "articles_count":0,
                "paywall_secured":false,
                "_links":{
                   "self":{
                      "href":"\/api\/v2\/content\/routes\/6"
                   },
                   "parent":{
                      "href":"\/api\/v2\/content\/routes\/3"
                   }
                }
             }
          ],
          "lft":3,
          "rgt":6,
          "level":0,
          "type":"collection",
          "cache_time_in_seconds":0,
          "name":"news",
          "slug":"news",
          "position":1,
          "articles_count":0,
          "paywall_secured":false,
          "_links":{
             "self":{
                "href":"\/api\/v2\/content\/routes\/3"
             }
          }
       },
       "is_publishable":false,
       "metadata":{
          "subject":[

          ],
          "urgency":0,
          "priority":5,
          "place":[

          ],
          "service":[
             {
                "code":"b",
                "name":"Business"
             }
          ],
          "type":"text",
          "byline":"test embedded image in body",
          "guid":"urn:newsml:spsd.pro:2018-09-04T13:30:12.108807:89f823eb-6a6f-4ec9-ac09-06f5b95695d6",
          "language":"en"
       },
       "lead":"test image in body",
       "code":"urn:newsml:spsd.pro:2018-09-04T13:30:12.108807:89f823eb-6a6f-4ec9-ac09-06f5b95695d6",
       "sources":[

       ],
       "extra":[

       ],
       "slideshows":[

       ],
       "previous_relative_urls":[

       ],
       "created_at":"2019-03-10T09:00:00+00:00",
       "updated_at":"2019-03-10T09:00:00+00:00",
       "authors":[
          {
             "name":"Admin Person",
             "role":"featured",
             "jobtitle":[

             ],
             "biography":"",
             "id":6,
             "slug":"admin-person"
          }
       ],
       "keywords":[

       ],
       "media":[

       ],
       "is_published_fbia":false,
       "article_statistics":{
          "impressions_number":0,
          "page_views_number":0,
          "internal_click_rate":0,
          "created_at":"2019-03-10T09:00:00+00:00",
          "updated_at":"2019-03-10T09:00:00+00:00"
       },
       "comments_count":0,
       "is_published_to_apple_news":false,
       "tenant":{
          "id":1,
          "domain_name":"localhost",
          "name":"Default tenant",
          "code":"123abc",
          "amp_enabled":true,
          "_links":{
             "self":{
                "href":"\/api\/v2\/tenants\/123abc"
             }
          },
          "default_language":"",
          "fbia_enabled":false,
          "paywall_enabled":false
       },
       "paywall_secured":false,
       "content_lists":[

       ],
       "_links":{
          "self":{
             "href":"\/api\/v2\/content\/articles\/test-image-in-body"
          },
          "online":{
             "href":"\/news\/test-image-in-body"
          },
          "related":{
             "href":"\/api\/v2\/content\/articles\/6\/related\/"
          },
          "slideshows":{
             "href":"\/api\/v2\/content\/slideshows\/6"
          }
       }
    }
    """
