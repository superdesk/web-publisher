@content_push
Feature: Handling video groups
  In order to be able to display video in the slideshows
  As a HTTP Client
  I want to able to receive and parse the request payload with video groups

  Scenario: Saving the data with video groups
    Given I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/assets/push" with parameters:
      | key          | value                                                                                |
      | media_id     | 20180904130932/b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4  |
      | media        | @video.mp4                                                                           |
    Then the response status code should be 201
    And the JSON node "media_id" should be equal to "20180904130932/b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4"
    And the JSON node "mime_type" should be equal to "video/mp4"
    And the JSON node "URL" should be equal to "http://localhost/media/20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4"
    And the JSON node "media" should not be null

    When I add "Content-Type" header equal to "application/json"
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
        "slideshow1": {
          "items":[
            {
              "renditions":{
                "original":{
                  "media":"20180904130932/b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4",
                  "href":"https://sdaws.com/sd-sp/20180904130932/b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4",
                  "mimetype":"video/mp4"
                }
              },
              "version":"2",
              "guid":"tag:spsd.pro:2018:626bee3c-7e5d-4e59-b860-e3d13c992b86",
              "copyrightholder":"",
              "copyrightnotice":"",
              "usageterms":"",
              "source":"",
              "description_text":"inline video",
              "urgency":3,
              "genre":[
                {
                  "code":"Article",
                  "name":"Article (news)"
                }
              ],
              "body_text":"inline video",
              "firstcreated":"2018-09-04T11:33:00+0000",
              "pubstatus":"usable",
              "priority":6,
              "headline":"inline video",
              "language":"en",
              "mimetype":"video/mp4",
              "versioncreated":"2018-09-04T11:33:01+0000",
              "type":"video"
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
      "headline":"testing correction",
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
          "destinations":[
            {
              "tenant":"123abc",
              "published":true
            }
          ]
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | media[0].file.asset_id         | 20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9            |
      | media[0].file.fileExtension   | mp4                                                                                        |
      | media[0]._links.download.href | /media/20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4 |
      | slideshows[0].code            | slideshow1                                                                                 |
      | _links.slideshows.href        | /api/v1/content/slideshows/6                                                               |
    And the JSON node "media[0].image" should be null

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/slideshows/6"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 1
    And the JSON node "_embedded._items[0].code" should be equal to "slideshow1"
    And the JSON node "_embedded._items[0].article.title" should be equal to "testing correction"
    And the JSON node "_embedded._items[0]._links.items.href" should be equal to "/api/v1/content/slideshows/6/1/items/"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/slideshows/6/1/items/"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 1
    And the JSON node "_embedded._items[0].articleMedia.file.fileExtension" should be equal to "mp4"
    And the JSON node "_embedded._items[0].articleMedia.file.asset_id" should be equal to "20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9"
    And the JSON node "_embedded._items[0].articleMedia._links.download.href" should be equal to "/media/20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4"
    And the JSON node "_embedded._items[0].articleMedia.image" should be null
