@packages
Feature: Get related articles by package id
  In order to be able to display related articles
  As a HTTP Client
  I want to able to receive and parse the request with related items payload

  Scenario: Pushing the content with related items data
    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
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
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test-1-0123456789abc"
    Then the response status code should be 200

    Then I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
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
              "slugline":"abstract-html-test-2",
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
      "pubstatus":"usable",
      "associations":{
        "related--item1":{
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
        "related--item2":{
          "language":"en",
          "slugline":"abstract-html-test-2",
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
      }
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/7/publish/" with body:
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
    Then I send a "GET" request to "/api/v2/content/articles/abstract-html-test"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | _links.related.href  | /api/v2/content/articles/7/related/  |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/7/related/"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | related_article_items[0].tenants[0].code | 123abc        |
      | related_article_items[0].title           | hello world 1 |
      | related_article_items[1].title           | hello world 2 |
    And the JSON node "related_article_items[1].tenants[0]" should not exist
