@content_push
Feature: Handling embedded video
  In order to be able to display video inside the article body
  As a HTTP Client
  I want to able to receive and parse the request payload with video inside the body

  Scenario: Saving the data with embedded video
    Given I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key          | value                                                                                |
      | media_id     | 20180904130932/b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4  |
      | media        | @video.mp4                                                                           |
    Then the response status code should be 201
    And the JSON node "media_id" should be equal to "20180904130932/b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4"
    And the JSON node "mime_type" should be equal to "video/mp4"
    And the JSON node "URL" should be equal to "http://localhost/uploads/swp/123456/media/20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4"
    And the JSON node "media" should not be null

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "version":"3",
      "slugline":"test video in body",
      "copyrightholder":"",
      "copyrightnotice":"",
      "service":[
        {
          "code":"b",
          "name":"Business"
        }
      ],
      "language":"en",
      "byline":"test video in body",
      "associations":{
        "editor_0":{
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
      },
      "profile":"news",
      "description_html":"<p>test video in body</p>",
      "pubstatus":"usable",
      "firstcreated":"2018-09-04T11:30:12+0000",
      "headline":"test video in body",
      "body_html":"<p>test video in body</p><div class=\"media-block\"><video controls src=\"https://sdaws.com/sd-sp/20180904130932/b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4\" alt=\"inline video\" width=\"100%\" height=\"100%\" /><span class=\"media-block__description\">inline video</span></div><p>new line</p>",
      "priority":5,
      "type":"text",
      "description_text":"test video in body",
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
      "source":"superdesk publisher",
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
              "published":true
            }
          ]
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/test-video-in-body"
    Then the response status code should be 200
    And the JSON node "body" should contain "/media/20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4"
    And the JSON nodes should contain:
      | media[0].file.asset_id         | 20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9            |
      | media[0].file.file_extension   | mp4                                                                                        |
      | media[0]._links.download.href | /media/20180904130932_b42edf4c501057a44499c8148d60a6343fb0e968150fc538404b5b72ed9279b9.mp4 |
    And the JSON node "media[0].image" should be null
