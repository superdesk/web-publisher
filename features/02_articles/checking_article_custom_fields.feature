@articles
Feature: Validate custom fields
  In order to handle custom fields
  As a HTTP Client
  I want to be able to check if the custom fields were processed properly

  Scenario: Submitting and publishing a package with extra custom fields
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "multipart/form-data"
    And I send a "POST" request to "/api/v2/assets/push" with parameters:
      | key      | value      |
      | media    | @image.jpg |
      | media_id | 20180131130152/f4dacebedb22ae2d67a97cdc059aef3165bd3a73affa316a7c2d397dc6ead14b.jpg |
    Then the response status code should be 201
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "language": "en",
      "byline":"John Jones",
      "source":"Sourcefabric",
      "type":"text",
      "description_text":"Lorem ipsum abstract",
      "guid":"urn:newsml:sd-master.test.superdesk.org:2018-01-18T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc",
      "profile":"Article",
      "subject":[
        {
          "code":"01001000",
          "name":"archaeology"
        }
      ],
      "wordcount":3,
      "urgency":3,
      "authors":[
        {
          "biography":"bioquil",
          "name":"Nareg Asmarian",
          "jobtitle":{
            "qcode":"1",
            "name":"quality check"
          },
          "role":"writer"
        }
      ],
      "copyrightholder":"",
      "slugline":"testing authors",
      "headline":"testing authors",
      "version":"3",
      "description_html":"<p>Lorem ipsum abstract</p>",
      "located":"Prague",
      "pubstatus":"usable",
      "copyrightnotice":"",
      "body_html":"<p>Lorem ipsum body</p>",
      "usageterms":"",
      "priority":6,
      "genre":[
        {
          "code":"Article",
          "name":"Article (news)"
        }
      ],
      "versioncreated":"2018-01-18T09:31:58+0000",
      "firstpublished":"2018-01-18T09:31:58+0000",
      "charcount":16,
      "extra":{
        "custom-date":"2018-01-18T00:00:00+0000",
        "ID":"<p>custom botttom field text</p>",
        "limit-test":"<p>limit test field</p>",
        "rafal-embed":{
            "embed":"embed link",
            "description":"Shakin' Stevens"
        }
      },
      "service":[
        {
          "code":"f",
          "name":"sports"
        }
      ],
      "readtime":0,
      "firstcreated":"2018-01-18T09:26:52+0000",
      "associations":{
        "rafal-media-field--1":{
            "priority":6,
            "guid":"tag:sd-master.test.superdesk.org:2018:db9d5ffd-4bbe-463c-a514-f97d75ad51c4",
            "firstcreated":"2018-01-31T13:53:29+0000",
            "language":"en",
            "source":"Superdesk",
            "copyrightholder":"",
            "body_text":"alt text",
            "type":"picture",
            "pubstatus":"usable",
            "copyrightnotice":"",
            "located":"Prague",
            "mimetype":"image/jpeg",
            "versioncreated":"2018-01-31T13:53:30+0000",
            "headline":"cosmos image",
            "usageterms":"",
            "byline":"John Jones",
            "version":"2",
            "urgency":3,
            "genre":[
                {
                    "code":"Article",
                    "name":"Article (news)"
                }
            ],
            "renditions":{
                "baseImage":{
                    "mimetype":"image/jpeg",
                    "width":933,
                    "href":"https://sd-master.test.superdesk.org/api/upload-raw/20180131130152/3d993efd0cc466e4ccd83abe070ef9f1538155576dfef9aaec18be1bce3e32cf.jpg",
                    "poi":{
                        "y":700,
                        "x":466
                    },
                    "height":1400,
                    "media":"20180131130152/3d993efd0cc466e4ccd83abe070ef9f1538155576dfef9aaec18be1bce3e32cf.jpg"
                },
                "ff":{
                    "mimetype":"image/jpeg",
                    "width":700,
                    "href":"https://sd-master.test.superdesk.org/api/upload-raw/20180131130152/92ee81e0-27a7-495c-8bb9-e97e0105d7e9.jpg",
                    "poi":{
                        "y":481,
                        "x":1728
                    },
                    "height":200,
                    "media":"20180131130152/92ee81e0-27a7-495c-8bb9-e97e0105d7e9.jpg"
                },
                "thumbnail":{
                    "mimetype":"image/jpeg",
                    "width":79,
                    "href":"https://sd-master.test.superdesk.org/api/upload-raw/20180131130152/11bf536fd8003bdd07897f51f6e8500de84ee361650b6129e0afb3224d274057.jpg",
                    "poi":{
                        "y":60,
                        "x":39
                    },
                    "height":120,
                    "media":"20180131130152/11bf536fd8003bdd07897f51f6e8500de84ee361650b6129e0afb3224d274057.jpg"
                },
                "viewImage":{
                    "mimetype":"image/jpeg",
                    "width":426,
                    "href":"https://sd-master.test.superdesk.org/api/upload-raw/20180131130152/5a701412b122e607bb7470b280863fee74dd039fd34e3ff156131d5c3ebc0f65.jpg",
                    "poi":{
                        "y":320,
                        "x":213
                    },
                    "height":640,
                    "media":"20180131130152/5a701412b122e607bb7470b280863fee74dd039fd34e3ff156131d5c3ebc0f65.jpg"
                },
                "original":{
                    "mimetype":"image/jpeg",
                    "width":3456,
                    "href":"https://sd-master.test.superdesk.org/api/upload-raw/20180131130152/f4dacebedb22ae2d67a97cdc059aef3165bd3a73affa316a7c2d397dc6ead14b.jpg",
                    "poi":{
                        "y":2592,
                        "x":1728
                    },
                    "height":5184,
                    "media":"20180131130152/f4dacebedb22ae2d67a97cdc059aef3165bd3a73affa316a7c2d397dc6ead14b.jpg"
                },
                "FIXME":{
                    "mimetype":"image/jpeg",
                    "width":800,
                    "href":"https://sd-master.test.superdesk.org/api/upload-raw/20180131130152/9f1d2961-9c94-4b6e-880e-f129a52afdc3.jpg",
                    "poi":{
                        "y":1296,
                        "x":1728
                    },
                    "height":600,
                    "media":"20180131130152/9f1d2961-9c94-4b6e-880e-f129a52afdc3.jpg"
                }
            },
            "description_text":"cosmos image"
        }
      }
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/content/routes/" with body:
     """
      {
          "name":"article",
          "type":"content"
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
              "route":6,
              "is_published_fbia":false
            }
          ]
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/testing-authors"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | slug                          | testing-authors                                                                 |
      | extra.custom-date             | 2018-01-18T00:00:00+0000                                                        |
      | extra.ID                      | <p>custom botttom field text</p>                                                |
      | extra.limit-test              | <p>limit test field</p>                                                         |
      | extra.rafal-embed.description | Shakin' Stevens                                                                 |
      | extra.rafal-embed.embed       | embed link                                                                      |
      | media[0].image.asset_id        | 20180131130152_f4dacebedb22ae2d67a97cdc059aef3165bd3a73affa316a7c2d397dc6ead14b |
