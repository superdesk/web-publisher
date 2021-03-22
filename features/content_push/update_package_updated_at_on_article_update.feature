@content_push
Feature: Updating package's updated at timestamp when the article is updated
  In order to show a proper order of articles both in Output Control and Content Lists
  As a HTTP Client
  I want to be able to check if the order of packages and articles is the same when sorting by updated_at timestamp

  Scenario: Package status is not updated after the correction
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
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
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | headline                | testing correction              |
      | status                  | new                             |
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/6/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "route":6,
              "is_published_fbia":false,
              "published":true
            }
          ]
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | headline                | testing correction              |
      | status                  | published                       |

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
        {
      "language":"en",
      "evolvedfrom":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0",
      "slugline":"abstract-html-test-corrected",
      "body_html":"<p>some html body</p>",
      "versioncreated":"2017-02-23T13:57:28+0000",
      "firstcreated":"2017-05-25T10:23:15+0000",
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
      "version":"2",
      "byline":"ADmin",
      "keywords":[
        "keyword1",
        "keyword2"
      ],
      "guid":"urn:newsml:localhost:2017-02-02T11:26:59.404843:7u465de4-0d5c-495a-2u36-3b986def3k81",
      "priority":6,
      "subject":[
        {
          "name":"lawyer",
          "code":"02002001"
        }
      ],
      "urgency":3,
      "type":"text",
      "headline":"testing correction corrected",
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
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | headline                | testing correction corrected    |
      | status                  | published                       |
      | slugline                | abstract-html-test-corrected    |
    And print last JSON response
    And the JSON node "updated_at" should exist
    And we save it into "package_updated_at"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/?limit=20&page=1&sorting%5Bupdated_at%5D=desc&status%5B%5D=published&status%5B%5D=unpublished"
    Then the response status code should be 200
    And the JSON node "_embedded._items[0].updated_at" should be equal to "<<package_updated_at>>"
    And the JSON node "_embedded._items[0].articles[0].updated_at" should be equal to "<<package_updated_at>>"
    And the JSON node "_embedded._items[0].headline" should be equal to "testing correction corrected"

    And I wait 3 seconds

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?limit=20&page=1&sorting%5Bupdated_at%5D=desc&status%5B%5D=published&status%5B%5D=unpublished"
    Then the response status code should be 200
    And print last JSON response
    And the JSON node "_embedded._items[0].updated_at" should be equal to "<<package_updated_at>>"
    And the JSON node "_embedded._items[0].title" should be equal to "testing correction corrected"

    And I wait 3 seconds

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/articles/abstract-html-test" with body:
     """
      {
          "seo_metadata": {
              "meta_description": "This is my meta description"
          }
      }
     """
    Then the response status code should be 200

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200

    And the JSON node "updated_at" should exist
    And we save it into "package_updated_at"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/?limit=20&page=1&sorting%5Bupdated_at%5D=desc&status%5B%5D=published&status%5B%5D=unpublished"
    Then the response status code should be 200
    And the JSON node "_embedded._items[0].updated_at" should be equal to "<<package_updated_at>>"
    And the JSON node "_embedded._items[0].articles[0].updated_at" should be equal to "<<package_updated_at>>"
    And the JSON node "_embedded._items[0].headline" should be equal to "testing correction corrected"
