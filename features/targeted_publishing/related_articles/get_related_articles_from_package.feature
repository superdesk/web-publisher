@targeted_publishing
Feature: Find related articles which were already published based on package data

  Scenario: Find related articles based on package data
    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/organization/rules/" with body:
     """
      {
          "name":"Test rule",
          "description":"Test rule description",
          "priority":1,
          "expression":"package.getSource() matches \"/agency/\"",
          "configuration":[
            {
              "key":"destinations",
              "value":[
                {
                  "tenant":"123abc"
                },
                {
                  "tenant":"678iop"
                }
              ]
            }
          ]
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/rules/" with body:
     """
      {
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"article.getMetadataByKey(\"source\") matches \"/agency/\"",
          "configuration":[
            {
              "key":"route",
              "value":6
            }
          ]
      }
     """
    Then the response status code should be 201

    Given I am authenticated as "test.client2"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "http://client2.localhost/api/v2/content/routes/" with body:
     """
      {
          "name": "My route",
          "type": "collection"
      }
    """
    Then the response status code should be 201
    And the JSON node "id" should be equal to "7"

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"678iop",
          "route":7,
          "is_published_fbia":false,
          "published":true,
          "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2019-02-28T12:17:59.728688:e51c6cef-2c23-4a15-abdc-fb9f37141d55"
      }
    """
    Then the response status code should be 200

    Then I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "type":"text",
      "pubstatus":"usable",
      "guid":"urn:newsml:sd-master.test.superdesk.org:2019-02-28T12:17:59.728688:e51c6cef-2c23-4a15-abdc-fb9f37141d55",
      "versioncreated":"2019-02-28T12:18:28+0000",
      "body_html":"<p><b>related 1</b></p>",
      "wordcount":2,
      "version":"1",
      "usageterms":"",
      "language":"en",
      "description_html":"<p><b>related 1</b></p>",
      "genre":[
        {
          "code":"Article",
          "name":"Article (news)"
        }
      ],
      "copyrightnotice":"",
      "priority":6,
      "readtime":0,
      "subject":[
        {
          "code":"01001000",
          "name":"archaeology"
        }
      ],
      "authors":[
        {
          "role":"writer",
          "biography":"",
          "name":"Andrew Powers"
        }
      ],
      "description_text":"related 1",
      "copyrightholder":"",
      "annotations":[

      ],
      "urgency":3,
      "headline":"related 1",
      "source":"agency",
      "slugline":"related 1",
      "place":[
        {
          "code":"ontario",
          "name":"ontario"
        }
      ],
      "firstcreated":"2019-02-28T12:17:59+0000",
      "profile":"relateditems",
      "charcount":9
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/related-1-0123456789abc"
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "http://client2.localhost/api/v2/content/articles/related-1-0123456789abc"
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/organization/articles/related/" with body:
    """
    {
      "type":"text",
      "pubstatus":"usable",
      "associations":{
        "related_items--1":{
          "type":"text",
          "pubstatus":"usable",
          "guid":"urn:newsml:sd-master.test.superdesk.org:2019-02-28T12:17:59.728688:e51c6cef-2c23-4a15-abdc-fb9f37141d55",
          "versioncreated":"2019-02-28T12:18:28+0000",
          "body_html":"<p><b>related 1</b></p>",
          "wordcount":2,
          "version":"1",
          "usageterms":"",
          "language":"en",
          "description_html":"<p><b>related 1</b></p>",
          "genre":[
            {
              "code":"Article",
              "name":"Article (news)"
            }
          ],
          "copyrightnotice":"",
          "priority":6,
          "readtime":0,
          "subject":[
            {
              "code":"01001000",
              "name":"archaeology"
            }
          ],
          "authors":[
            {
              "role":"writer",
              "biography":"",
              "name":"Andrew Powers"
            }
          ],
          "description_text":"related 1",
          "copyrightholder":"",
          "annotations":[

          ],
          "urgency":3,
          "headline":"related 1",
          "source":"agency",
          "slugline":"related 1",
          "place":[
            {
              "code":"ontario",
              "name":"ontario"
            }
          ],
          "firstcreated":"2019-02-28T12:17:59+0000",
          "profile":"relateditems",
          "charcount":9
        }
      },
      "extra_items":{
        "related_items":{
          "type":"related_content",
          "items":[
            {
              "type":"text",
              "pubstatus":"usable",
              "guid":"urn:newsml:sd-master.test.superdesk.org:2019-02-28T12:17:59.728688:e51c6cef-2c23-4a15-abdc-fb9f37141d55",
              "versioncreated":"2019-02-28T12:18:28+0000",
              "body_html":"<p><b>related 1</b></p>",
              "wordcount":2,
              "version":"1",
              "usageterms":"",
              "language":"en",
              "description_html":"<p><b>related 1</b></p>",
              "genre":[
                {
                  "code":"Article",
                  "name":"Article (news)"
                }
              ],
              "copyrightnotice":"",
              "priority":6,
              "readtime":0,
              "subject":[
                {
                  "code":"01001000",
                  "name":"archaeology"
                }
              ],
              "authors":[
                {
                  "role":"writer",
                  "biography":"",
                  "name":"Andrew Powers"
                }
              ],
              "description_text":"related 1",
              "copyrightholder":"",
              "annotations":[

              ],
              "urgency":3,
              "headline":"related 1",
              "source":"agency",
              "slugline":"related 1",
              "place":[
                {
                  "code":"ontario",
                  "name":"ontario"
                }
              ],
              "firstcreated":"2019-02-28T12:17:59+0000",
              "profile":"relateditems",
              "charcount":9
            }
          ]
        }
      },
      "versioncreated":"2019-02-28T12:20:34+0000",
      "firstpublished":"2019-02-28T12:20:33+0000",
      "body_html":"<p>main article</p>",
      "headline":"main article",
      "wordcount":2,
      "version":"5",
      "usageterms":"",
      "language":"en",
      "genre":[
        {
          "code":"Article",
          "name":"Article (news)"
        }
      ],
      "copyrightnotice":"",
      "priority":6,
      "readtime":0,
      "subject":[
        {
          "code":"01001000",
          "name":"archaeology"
        }
      ],
      "authors":[
        {
          "role":"writer",
          "biography":"",
          "name":"Andrew Powers"
        }
      ],
      "description_text":"main article",
      "copyrightholder":"",
      "annotations":[

      ],
      "guid":"urn:newsml:sd-master.test.superdesk.org:2019-02-28T12:20:34.503999:34349280-d6c2-4167-9fd9-658efc668715",
      "service":[
        {
          "code":"f7",
          "name":"ONE "
        }
      ],
      "urgency":3,
      "description_html":"<p>main article</p>",
      "source":"agency",
      "slugline":"main article",
      "place":[
        {
          "code":"ontario",
          "name":"ontario"
        }
      ],
      "firstcreated":"2019-02-28T12:18:40+0000",
      "profile":"relateditems",
      "charcount":12
    }
    """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | related_article_items[0].tenants[0].code  | 678iop    |
      | related_article_items[0].tenants[1].code  | 123abc    |
      | related_article_items[0].title            | related 1 |
