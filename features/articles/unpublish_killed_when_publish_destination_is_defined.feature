@articles
@disable-fixtures
Feature: Unpublish corrected article when kill signal is sent and the publish destination is specified
  In order to kill already published article with corrections
  As a HTTP Client
  I want to be able to send ninjs payload with kill signal and unpublish article

  Scenario: Kill article
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      |  code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc  |

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given the following organization publishing rule:
     """
      {
          "name":"Test rule",
          "description":"Test rule description",
          "priority":1,
          "expression":"package.getLocated() matches \"/Sydney/\"",
          "configuration":[
            {
              "key":"destinations",
              "value":[
                {
                  "tenant":"123abc"
                }
              ]
            }
          ]
      }
     """

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"123abc",
          "route":1,
          "is_published_fbia":false,
          "published":true,
          "package_guid": "urn:newsml:localhost:2019-05-28T12:19:18.951780:34bedd17-9d28-4f89-afc3-dff84f44a17d"
      }
    """
    Then the response status code should be 200

    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
      {"description_html": "<p><b>kill</b> <b>correction</b> <b>test</b></p>", "body_html": "<p><b>kill</b> <b>correction</b> <b>test body</b></p>", "copyrightholder": "", "versioncreated": "2019-05-28T10:20:30+0000", "headline": "kill correction test", "annotations": [], "version": "2", "firstpublished": "2019-05-28T10:20:30+0000", "wordcount": 4, "usageterms": "", "type": "text", "pubstatus": "usable", "genre": [{"code": "Opinion", "name": "Opinion"}], "description_text": "killcorrectiontest", "charcount": 23, "guid": "urn:newsml:localhost:2019-05-28T12:19:18.951780:34bedd17-9d28-4f89-afc3-dff84f44a17d", "priority": 5, "readtime": 0, "service": [{"code": "p", "name": "Politics"}], "firstcreated": "2019-05-28T10:19:18+0000", "source": "superdesk publisher", "copyrightnotice": "", "language": "en", "profile": "news"}
    """
    Then the response status code should be 201

    When I go to "/test/kill-correction-test"
    Then the response status code should be 200

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/kill-correction-test"
    Then the response status code should be 200
    And the JSON node "status" should be equal to "published"

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
      {"description_html": "<p><b>kill</b> <b>correction</b> <b>test</b></p>", "body_html": "<p><b>kill</b> <b>correction</b> <b>test body corrected</b></p>", "copyrightholder": "", "versioncreated": "2019-05-28T10:21:13+0000", "headline": "kill correction test", "annotations": [], "version": "3", "firstpublished": "2019-05-28T10:20:30+0000", "wordcount": 5, "usageterms": "", "type": "text", "pubstatus": "usable", "genre": [{"code": "Opinion", "name": "Opinion"}], "description_text": "killcorrectiontest", "charcount": 33, "guid": "urn:newsml:localhost:2019-05-28T12:19:18.951780:34bedd17-9d28-4f89-afc3-dff84f44a17d", "priority": 5, "readtime": 0, "service": [{"code": "p", "name": "Politics"}], "firstcreated": "2019-05-28T10:19:18+0000", "source": "superdesk publisher", "copyrightnotice": "", "language": "en", "profile": "news"}
    """

    When I go to "/test/kill-correction-test"
    Then the response status code should be 200

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/kill-correction-test"
    Then the response status code should be 200
    And the JSON node "status" should be equal to "published"

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
      {"description_html": "<p>Please remove this article</p>", "body_html": "<p>Please kill story slugged headlined kill correction test ex at 28 May 2019 12:21 CEST.</p><p>Remove Remove Remove!</p>", "copyrightholder": "", "versioncreated": "2019-05-28T10:22:51+0000", "headline": "Kill/Takedown notice", "annotations": [], "version": "4", "firstpublished": "2019-05-28T10:20:30+0000", "wordcount": 18, "usageterms": "", "type": "text", "pubstatus": "canceled", "genre": [{"code": "Opinion", "name": "Opinion"}], "description_text": "Please remove this article", "charcount": 108, "guid": "urn:newsml:localhost:2019-05-28T12:19:18.951780:34bedd17-9d28-4f89-afc3-dff84f44a17d", "priority": 5, "readtime": 0, "service": [{"code": "p", "name": "Politics"}], "firstcreated": "2019-05-28T10:19:18+0000", "source": "superdesk publisher", "copyrightnotice": "", "language": "en", "profile": "news"}
    """
    Then the response status code should be 201

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/kill-correction-test"
    Then the response status code should be 200
    And the JSON node "status" should be equal to "canceled"

    When I go to "/test/kill-correction-test"
    Then the response status code should be 404
