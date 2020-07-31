@content_push
Feature: Allow to change the article's slug based on settings.
  In order to change article's slugline on package correction
  As a HTTP Client
  I want to be able to override it by changing the settings

  Scenario: Override slugline
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "readtime":57,
      "ednote":"In the story \"datenschutz\"  sent at: 29/01/2019 15:35\r\n\r\nThis is corrected repeat.",
      "copyrightnotice":"",
      "type":"text",
      "charcount":113634,
      "versioncreated":"2019-01-29T15:11:06+0000",
      "language":"en",
      "firstpublished":"2019-01-14T09:51:49+0000",
      "body_html":"<h1>Richtlinien zum Datenschutz</h1>",
      "copyrightholder":"",
      "urgency":3,
      "headline":"Richtlinien zum Datenschutz",
      "extra":{
        "seotitle":"Richtlinien zum Datenschutz"
      },
      "usageterms":"",
      "wordcount":14364,
      "version":"7",
      "profile":"NachrichtenFG",
      "pubstatus":"usable",
      "priority":6,
      "service":[
        {
          "code":"serv",
          "name":"Service"
        }
      ],
      "firstcreated":"2019-01-14T09:09:56+0000",
      "annotations":[

      ],
      "source":"fg",
      "guid":"dc8fcbb4-22f2-4e0b-bd24-7714ce8d865a"
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | headline                | Richtlinien zum Datenschutz     |
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
              "isPublishedFbia":false,
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
      | headline                | Richtlinien zum Datenschutz     |
      | status                  | published                       |

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
     {
      "readtime":57,
      "ednote":"In the story \"datenschutz\"  sent at: 29/01/2019 15:35\r\n\r\nThis is corrected repeat.",
      "copyrightnotice":"",
      "type":"text",
      "charcount":113634,
      "versioncreated":"2019-01-29T15:11:06+0000",
      "language":"en",
      "firstpublished":"2019-01-14T09:51:49+0000",
      "body_html":"<h1>Richtlinien zum Datenschutz</h1>",
      "copyrightholder":"",
      "urgency":3,
      "headline":"Richtlinien zum Datenschutz",
      "extra":{
        "seotitle":"Richtlinien zum Datenschutz"
      },
      "usageterms":"",
      "wordcount":14364,
      "version":"7",
      "profile":"NachrichtenFG",
      "pubstatus":"usable",
      "priority":6,
      "service":[
        {
          "code":"serv",
          "name":"Service"
        }
      ],
      "firstcreated":"2019-01-14T09:09:56+0000",
      "slugline":"datenschutz",
      "annotations":[

      ],
      "source":"fg",
      "guid":"dc8fcbb4-22f2-4e0b-bd24-7714ce8d865a"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | headline                | Richtlinien zum Datenschutz     |
      | status                  | published                       |
      | slugline                | datenschutz                     |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | title                   | Richtlinien zum Datenschutz     |
      | status                  | published                       |
    And the JSON node "slug" should be equal to "richtlinien-zum-datenschutz"

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/" with body:
    """
    {
        "name":"override_slug_on_correction",
        "value":true
    }
    """
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
     {
      "readtime":57,
      "ednote":"In the story \"datenschutz\"  sent at: 29/01/2019 15:35\r\n\r\nThis is corrected repeat.",
      "copyrightnotice":"",
      "type":"text",
      "charcount":113634,
      "versioncreated":"2019-01-29T15:11:06+0000",
      "language":"en",
      "firstpublished":"2019-01-14T09:51:49+0000",
      "body_html":"<h1>Richtlinien zum Datenschutz</h1>",
      "copyrightholder":"",
      "urgency":3,
      "headline":"Richtlinien zum Datenschutz",
      "extra":{
        "seotitle":"Richtlinien zum Datenschutz"
      },
      "usageterms":"",
      "wordcount":14364,
      "version":"7",
      "profile":"NachrichtenFG",
      "pubstatus":"usable",
      "priority":6,
      "service":[
        {
          "code":"serv",
          "name":"Service"
        }
      ],
      "firstcreated":"2019-01-14T09:09:56+0000",
      "slugline":"datenschutz",
      "annotations":[

      ],
      "source":"fg",
      "guid":"dc8fcbb4-22f2-4e0b-bd24-7714ce8d865a"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | headline                | Richtlinien zum Datenschutz     |
      | status                  | published                       |
      | slugline                | datenschutz                     |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/6"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | title                   | Richtlinien zum Datenschutz     |
      | status                  | published                       |
    And the JSON node "slug" should be equal to "datenschutz"
    And the JSON node "previous_relative_urls[0].relative_url" should be equal to "/news/sports/richtlinien-zum-datenschutz"

    When I go to "/news/sports/datenschutz"
    Then the response status code should be 200

    When I send a GET request to "/news/sports/richtlinien-zum-datenschutz"
    Then the response status code should be 301
    And I follow the redirection
    Then the response status code should be 200
