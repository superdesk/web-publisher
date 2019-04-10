Feature: Use original image URL when an image can not be downloaded

  Scenario: Fallback to the original image url when it can not be downloaded
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/push" with body:
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
              "media":"20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg",
              "height":1365,
              "href":"https://amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg"
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
        }
      },
      "place":[

      ],
      "firstcreated":"2017-02-07T07:46:48+0000",
      "body_html":"<p>some text and after that we should get image</p>\n<!-- EMBED START Image {id: \"embedded9582903151\"} -->\n<figure><img src=\"https://amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg\" alt=\"Bell Peppers\" /><figcaption>Few of a kind</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded9582903151\"} -->\n<p>and after image again some text</p><p>footer content</p>",
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
    Then the response status code should be 201

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "technews",
          "slug": "technews",
          "type": "collection",
          "articlesTemplateName": "embedded_image.html.twig"
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
              "published":true,
              "route": 7
            }
          ]
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/item-test"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | media[0].by_line  | Ljub. Z. Ranković |
    And the JSON node "media[0].image" should be null

    When I go to "http://localhost/technews/item-test"
    Then the response status code should be 200
    And the response should contain "embedded9582903151"
    And the response should contain "https://amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93a.jpg"
    And the response should contain "(Photo: Ljub. Z. Ranković)"
