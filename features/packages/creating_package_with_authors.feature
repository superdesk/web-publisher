@packages
Feature: Checking if the package authors are saved properly
  In order to see package authors
  As a HTTP Client
  I want to be able to push JSON content which contains authors data and see it in the system

  Scenario: Submitting request payload containing authors data in ninjs format
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/content/push" with body:
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
        },
        {
          "biography":"not dead yet",
          "name":"vincer vincer",
          "role":"subeditor"
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
        "limit-test":"<p>limit test field</p>"
      },
      "service":[
        {
          "code":"f",
          "name":"sports"
        }
      ],
      "readtime":0,
      "firstcreated":"2018-01-18T09:26:52+0000"
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/packages/6"
    Then the response status code should be 200
    And the JSON node "authors" should exist
    And the JSON nodes should contain:
      | authors[0].name                | Nareg Asmarian              |
      | authors[0].biography           | bioquil                     |
      | authors[0].role                | writer                      |
      | authors[0].jobtitle.name       | quality check               |
      | authors[0].jobtitle.qcode      | 1                           |
      | authors[1].name                | vincer vincer               |
      | authors[1].biography           | not dead yet                |
      | authors[1].role                | subeditor                   |
    And the JSON node "authors[1].jobtitle.name" should not exist
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/content/routes/" with body:
     """
      {
        "route":{
          "name":"article",
          "type":"content"
        }
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/packages/6/publish/" with body:
     """
      {
        "publish":{
          "destinations":[
            {
              "tenant":"123abc",
              "route":6,
              "fbia":false,
              "published":true
            }
          ]
        }
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/content/articles/testing-authors"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | authors[0].name                | Nareg Asmarian              |
      | authors[0].biography           | bioquil                     |
      | authors[0].role                | writer                      |
      | authors[0].jobtitle.name       | quality check               |
      | authors[0].jobtitle.qcode      | 1                           |
      | authors[0].slug                | nareg-asmarian              |
      | authors[1].name                | vincer vincer               |
      | authors[1].biography           | not dead yet                |
      | authors[1].role                | subeditor                   |
      | authors[1].slug                | vincer-vincer               |

  Scenario: Submitting request payload containing authors data in ninjs format - updating
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/content/push" with body:
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
          "role":"writer",
          "avatar_url":"http://example.com/avatar1.png"
        },
        {
          "biography":"not dead yet",
          "name":"vincer vincer",
          "role":"subeditor",
          "avatar_url":"http://example.com/avatar2.png"
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
        "limit-test":"<p>limit test field</p>"
      },
      "service":[
        {
          "code":"f",
          "name":"sports"
        }
      ],
      "readtime":0,
      "firstcreated":"2018-01-18T09:26:52+0000"
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/packages/6/publish/" with body:
     """
      {
        "publish":{
          "destinations":[
            {
              "tenant":"123abc",
              "route":6,
              "fbia":false
            }
          ]
        }
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/content/articles/testing-authors"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | authors[0].name                | Nareg Asmarian                 |
      | authors[0].biography           | bioquil                        |
      | authors[0].role                | writer                         |
      | authors[0].avatar_url          | http://example.com/avatar1.png |
      | authors[0].jobtitle.name       | quality check                  |
      | authors[0].jobtitle.qcode      | 1                              |
      | authors[1].name                | vincer vincer                  |
      | authors[1].biography           | not dead yet                   |
      | authors[1].role                | subeditor                      |
      | authors[1].avatar_url          | http://example.com/avatar2.png |
      | authors[0].slug                | nareg-asmarian                 |
      | authors[1].slug                | vincer-vincer                  |
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/content/push" with body:
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
          "biography":"ed bio",
          "name":"ed",
          "role":"subeditor"
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
        "limit-test":"<p>limit test field</p>"
      },
      "service":[
        {
          "code":"f",
          "name":"sports"
        }
      ],
      "readtime":0,
      "firstcreated":"2018-01-18T09:26:52+0000"
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/packages/6"
    Then the response status code should be 200
    And the JSON node "authors" should exist
    And the JSON nodes should contain:
      | authors[0].name                | ed              |
      | authors[0].biography           | ed bio          |
      | authors[0].role                | subeditor       |
    And the JSON node "authors[0].jobtitle.name" should not exist
    And the JSON node "authors[0].avatar_url" should be null
    And the JSON node "authors[1]" should not exist
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/content/articles/testing-authors"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | authors[0].name                | ed              |
      | authors[0].biography           | ed bio          |
      | authors[0].role                | subeditor       |
      | authors[0].slug                | ed              |
    And the JSON node "authors[0].avatar_url" should be null
    And the JSON node "authors[1]" should not exist
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/content/push" with body:
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
          "biography":"ed bio",
          "name":"ed",
          "role":"subeditor"
        },
        {
          "biography":"not dead yet",
          "name":"vincer vincer",
          "role":"subeditor",
          "avatar_url":"http://example.com/avatar2.png"
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
        "limit-test":"<p>limit test field</p>"
      },
      "service":[
        {
          "code":"f",
          "name":"sports"
        }
      ],
      "readtime":0,
      "firstcreated":"2018-01-18T09:26:52+0000"
    }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/packages/6"
    Then the response status code should be 200
    And the JSON node "authors" should exist
    And the JSON nodes should contain:
      | authors[1].name                | ed                             |
      | authors[1].biography           | ed bio                         |
      | authors[1].role                | subeditor                      |
      | authors[0].name                | vincer vincer                  |
      | authors[0].biography           | not dead yet                   |
      | authors[0].role                | subeditor                      |
      | authors[0].avatar_url          | http://example.com/avatar2.png |
    And the JSON node "authors[1].jobtitle.name" should not exist
    And the JSON node "authors[1].avatar_url" should be null
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/{version}/content/articles/testing-authors"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | authors[1].name                | ed                             |
      | authors[1].biography           | ed bio                         |
      | authors[1].role                | subeditor                      |
      | authors[1].slug                | ed                             |
      | authors[0].name                | vincer vincer                  |
      | authors[0].biography           | not dead yet                   |
      | authors[0].role                | subeditor                      |
      | authors[0].avatar_url          | http://example.com/avatar2.png |
      | authors[0].slug                | vincer-vincer                  |
    And the JSON node "authors[1].avatar_url" should be null
