@packages
Feature: Getting package
  In order to get a package
  As a HTTP Client
  I want to be able to push JSON content with package and see it in the system

  Scenario: Submitting request payload in ninjs format
    Given I am authenticated as "test.user"
    And the current date time is "2019-03-10 09:00"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "language":"en",
      "slugline":"abstract-html-test-without-slideshow",
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
          "type": "media",
          "items":[
            {
              "renditions":{
                "16-9":{
                  "height":720,
                  "mimetype":"image/jpeg",
                  "width":1079,
                  "media":"1234567890987654321a",
                  "href":"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"
                },
                "4-3":{
                  "height":533,
                  "mimetype":"image/jpeg",
                  "width":800,
                  "media":"1234567890987654321b",
                  "href":"http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"
                },
                "original":{
                  "height":2667,
                  "mimetype":"image/jpeg",
                  "width":4000,
                  "media":"1234567890987654321c",
                  "href":"http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"
                }
              },
              "urgency":3,
              "body_text":"test image",
              "versioncreated":"2016-08-17T17:46:52+0000",
              "guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6",
              "byline":"Pawe\u0142 Miko\u0142ajczuk",
              "pubstatus":"usable",
              "language":"en",
              "version":"2",
              "description_text":"test image",
              "priority":6,
              "type":"picture",
              "service":[
                {
                  "name":"Australian General News",
                  "code":"a"
                }
              ],
              "usageterms":"indefinite-usage",
              "mimetype":"image/jpeg",
              "headline":"test image",
              "located":"Porto"
            },
            {
              "renditions":{
                "16-9":{
                  "height":720,
                  "mimetype":"image/jpeg",
                  "width":1079,
                  "media":"2234567890987654321a",
                  "href":"http://localhost:5000/api/upload/2234567890987654321a/raw?_schema=http"
                },
                "4-3":{
                  "height":533,
                  "mimetype":"image/jpeg",
                  "width":800,
                  "media":"2234567890987654321b",
                  "href":"http://localhost:5000/api/upload/2234567890987654321b/raw?_schema=http"
                },
                "original":{
                  "height":2667,
                  "mimetype":"image/jpeg",
                  "width":4000,
                  "media":"2234567890987654321c",
                  "href":"http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http"
                }
              },
              "urgency":3,
              "body_text":"test image",
              "versioncreated":"2016-08-17T17:46:52+0000",
              "guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a2",
              "byline":"Pawe\u0142 Miko\u0142ajczuk",
              "pubstatus":"usable",
              "language":"en",
              "version":"2",
              "description_text":"test image 2",
              "priority":6,
              "type":"picture",
              "service":[
                {
                  "name":"Australian General News",
                  "code":"a"
                }
              ],
              "usageterms":"indefinite-usage",
              "mimetype":"image/jpeg",
              "headline":"test image",
              "located":"Porto"
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
      "guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf1",
      "priority":6,
      "subject":[
        {
          "name":"lawyer",
          "code":"02002001"
        }
      ],
      "urgency":3,
      "type":"text",
      "headline":"Article with added slideshow",
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
          "subject":[
            {
              "code":"05004000",
              "name":"preschool"
            }
          ],
          "type":"picture",
          "usageterms":"indefinite-usage",
          "priority":6,
          "byline":"Ljub. Z. Rankovi\u0107",
          "urgency":3,
          "language":"en",
          "headline":"Smoke on the water",
          "versioncreated":"2017-01-11T14:52:05+0000",
          "description_text":"Smoke on the water on River Gradac\u00a0",
          "guid":"tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09",
          "body_text":"Gradac",
          "version":"5",
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
              "href":"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http"
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
              "href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http"
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
              "href":"http://localhost:3000/api/upload/1234567890987654321c/raw?_schema=http"
            }
          }
        },
        "slideshow1--1":{
          "renditions":{
            "16-9":{
              "height":720,
              "mimetype":"image/jpeg",
              "width":1079,
              "media":"1234567890987654321a",
              "href":"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"
            },
            "4-3":{
              "height":533,
              "mimetype":"image/jpeg",
              "width":800,
              "media":"1234567890987654321b",
              "href":"http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"
            },
            "original":{
              "height":2667,
              "mimetype":"image/jpeg",
              "width":4000,
              "media":"1234567890987654321c",
              "href":"http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"
            }
          },
          "urgency":3,
          "body_text":"test image",
          "versioncreated":"2016-08-17T17:46:52+0000",
          "guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6",
          "byline":"Pawe\u0142 Miko\u0142ajczuk",
          "pubstatus":"usable",
          "language":"en",
          "version":"2",
          "description_text":"test image",
          "priority":6,
          "type":"picture",
          "service":[
            {
              "name":"Australian General News",
              "code":"a"
            }
          ],
          "usageterms":"indefinite-usage",
          "mimetype":"image/jpeg",
          "headline":"test image",
          "located":"Porto"
        },
        "slideshow1--2":{
          "renditions":{
            "16-9":{
              "height":720,
              "mimetype":"image/jpeg",
              "width":1079,
              "media":"2234567890987654321a",
              "href":"http://localhost:5000/api/upload/2234567890987654321a/raw?_schema=http"
            },
            "4-3":{
              "height":533,
              "mimetype":"image/jpeg",
              "width":800,
              "media":"2234567890987654321b",
              "href":"http://localhost:5000/api/upload/2234567890987654321b/raw?_schema=http"
            },
            "original":{
              "height":2667,
              "mimetype":"image/jpeg",
              "width":4000,
              "media":"2234567890987654321c",
              "href":"http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http"
            }
          },
          "urgency":3,
          "body_text":"test image",
          "versioncreated":"2016-08-17T17:46:52+0000",
          "guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a2",
          "byline":"Pawe\u0142 Miko\u0142ajczuk",
          "pubstatus":"usable",
          "language":"en",
          "version":"2",
          "description_text":"test image 2",
          "priority":6,
          "type":"picture",
          "service":[
            {
              "name":"Australian General News",
              "code":"a"
            }
          ],
          "usageterms":"indefinite-usage",
          "mimetype":"image/jpeg",
          "headline":"test image",
          "located":"Porto"
        }
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    {"id":6,"guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf1","headline":"Article with added slideshow","byline":"ADmin","slugline":"abstract-html-test-without-slideshow","language":"en","subject":[{"name":"lawyer","code":"02002001"}],"type":"text","place":[{"country":"Australia","world_region":"Oceania","state":"Australian Capital Territory","qcode":"ACT","name":"ACT","group":"Australia"}],"service":[{"name":"Australian General News","code":"a"}],"located":"Warsaw","urgency":3,"priority":6,"version":2,"genre":null,"ednote":null,"description_text":"some abstract text","keywords":["keyword1","keyword2"],"pubstatus":"usable","evolvedfrom":null,"source":null,"extra":[],"firstpublished":null,"copyrightnotice":null,"copyrightholder":null,"authors":[],"associations":[{"id":1,"guid":"tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09","headline":"Smoke on the water","byline":"Ljub. Z. Ranković","slugline":null,"language":"en","subject":[{"code":"05004000","name":"preschool"}],"type":"picture","place":[],"service":[],"located":null,"urgency":3,"priority":6,"version":5,"genre":null,"ednote":null,"description_text":"Smoke on the water on River Gradac ","keywords":[],"pubstatus":"usable","evolvedfrom":null,"source":null,"extra":[],"firstpublished":null,"copyrightnotice":null,"copyrightholder":null,"authors":null,"associations":[],"renditions":[{"id":1,"name":"viewImage","href":"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http","width":640,"height":426,"mimetype":"image/jpeg","media":"20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg"},{"id":2,"name":"baseImage","href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http","width":1400,"height":933,"mimetype":"image/jpeg","media":"20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg"},{"id":3,"name":"original","href":"http://localhost:3000/api/upload/1234567890987654321c/raw?_schema=http","width":2048,"height":1365,"mimetype":"image/jpeg","media":"20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg"}],"body_html":null,"body_text":"Gradac","usageterms":"indefinite-usage"},{"id":2,"guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6","headline":"test image","byline":"Paweł Mikołajczuk","slugline":null,"language":"en","subject":[],"type":"picture","place":[],"service":[{"name":"Australian General News","code":"a"}],"located":"Porto","urgency":3,"priority":6,"version":2,"genre":null,"ednote":null,"description_text":"test image","keywords":[],"pubstatus":"usable","evolvedfrom":null,"source":null,"extra":[],"firstpublished":null,"copyrightnotice":null,"copyrightholder":null,"authors":null,"associations":[],"renditions":[{"id":4,"name":"16-9","href":"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http","width":1079,"height":720,"mimetype":"image/jpeg","media":"1234567890987654321a"},{"id":5,"name":"4-3","href":"http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http","width":800,"height":533,"mimetype":"image/jpeg","media":"1234567890987654321b"},{"id":6,"name":"original","href":"http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http","width":4000,"height":2667,"mimetype":"image/jpeg","media":"1234567890987654321c"}],"body_html":null,"body_text":"test image","usageterms":"indefinite-usage"},{"id":3,"guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a2","headline":"test image","byline":"Paweł Mikołajczuk","slugline":null,"language":"en","subject":[],"type":"picture","place":[],"service":[{"name":"Australian General News","code":"a"}],"located":"Porto","urgency":3,"priority":6,"version":2,"genre":null,"ednote":null,"description_text":"test image 2","keywords":[],"pubstatus":"usable","evolvedfrom":null,"source":null,"extra":[],"firstpublished":null,"copyrightnotice":null,"copyrightholder":null,"authors":null,"associations":[],"renditions":[{"id":7,"name":"16-9","href":"http://localhost:5000/api/upload/2234567890987654321a/raw?_schema=http","width":1079,"height":720,"mimetype":"image/jpeg","media":"2234567890987654321a"},{"id":8,"name":"4-3","href":"http://localhost:5000/api/upload/2234567890987654321b/raw?_schema=http","width":800,"height":533,"mimetype":"image/jpeg","media":"2234567890987654321b"},{"id":9,"name":"original","href":"http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http","width":4000,"height":2667,"mimetype":"image/jpeg","media":"2234567890987654321c"}],"body_html":null,"body_text":"test image","usageterms":"indefinite-usage"}],"extra_items":[{"id":"slideshow1","items":[{"id":4,"guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6","headline":"test image","byline":"Paweł Mikołajczuk","slugline":null,"language":"en","subject":[],"type":"picture","place":[],"service":[{"name":"Australian General News","code":"a"}],"located":"Porto","urgency":3,"priority":6,"version":2,"genre":null,"ednote":null,"description_text":"test image","keywords":[],"pubstatus":"usable","evolvedfrom":null,"source":null,"extra":[],"firstpublished":null,"copyrightnotice":null,"copyrightholder":null,"authors":null,"associations":[],"renditions":[{"id":10,"name":"16-9","href":"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http","width":1079,"height":720,"mimetype":"image/jpeg","media":"1234567890987654321a"},{"id":11,"name":"4-3","href":"http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http","width":800,"height":533,"mimetype":"image/jpeg","media":"1234567890987654321b"},{"id":12,"name":"original","href":"http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http","width":4000,"height":2667,"mimetype":"image/jpeg","media":"1234567890987654321c"}],"body_html":null,"body_text":"test image","usageterms":"indefinite-usage"},{"id":5,"guid":"tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a2","headline":"test image","byline":"Paweł Mikołajczuk","slugline":null,"language":"en","subject":[],"type":"picture","place":[],"service":[{"name":"Australian General News","code":"a"}],"located":"Porto","urgency":3,"priority":6,"version":2,"genre":null,"ednote":null,"description_text":"test image 2","keywords":[],"pubstatus":"usable","evolvedfrom":null,"source":null,"extra":[],"firstpublished":null,"copyrightnotice":null,"copyrightholder":null,"authors":null,"associations":[],"renditions":[{"id":13,"name":"16-9","href":"http://localhost:5000/api/upload/2234567890987654321a/raw?_schema=http","width":1079,"height":720,"mimetype":"image/jpeg","media":"2234567890987654321a"},{"id":14,"name":"4-3","href":"http://localhost:5000/api/upload/2234567890987654321b/raw?_schema=http","width":800,"height":533,"mimetype":"image/jpeg","media":"2234567890987654321b"},{"id":15,"name":"original","href":"http://localhost:5000/api/upload/2234567890987654321c/raw?_schema=http","width":4000,"height":2667,"mimetype":"image/jpeg","media":"2234567890987654321c"}],"body_html":null,"body_text":"test image","usageterms":"indefinite-usage"}],"type":"media"}],"body_html":"<p>some html body</p>","featured_media":{"id":1,"guid":"tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09","headline":"Smoke on the water","byline":"Ljub. Z. Ranković","slugline":null,"language":"en","subject":[{"code":"05004000","name":"preschool"}],"type":"picture","place":[],"service":[],"located":null,"urgency":3,"priority":6,"version":5,"genre":null,"ednote":null,"description_text":"Smoke on the water on River Gradac ","keywords":[],"pubstatus":"usable","evolvedfrom":null,"source":null,"extra":[],"firstpublished":null,"copyrightnotice":null,"copyrightholder":null,"authors":null,"associations":[],"renditions":[{"id":1,"name":"viewImage","href":"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http","width":640,"height":426,"mimetype":"image/jpeg","media":"20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg"},{"id":2,"name":"baseImage","href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http","width":1400,"height":933,"mimetype":"image/jpeg","media":"20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg"},{"id":3,"name":"original","href":"http://localhost:3000/api/upload/1234567890987654321c/raw?_schema=http","width":2048,"height":1365,"mimetype":"image/jpeg","media":"20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg"}],"body_html":null,"body_text":"Gradac","usageterms":"indefinite-usage"},"created_at":"2019-03-10T09:00:00+00:00","updated_at":"2019-03-10T09:00:00+00:00","articles":[],"status":"new"}
    """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/"
    Then the response status code should be 200
