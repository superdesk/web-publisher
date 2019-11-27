@packages
Feature: Checking if the package authors are saved properly
  In order to see package authors
  As a HTTP Client
  I want to be able to push JSON content which contains authors data and see it in the system

  Scenario: Submitting request payload containing authors data in ninjs format
    Given I am authenticated as "test.user"
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
          "role":"writer",
          "twitter":"@superdeskman",
          "instagram":"superdeskman",
          "facebook":"superdeskman"
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
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON node "authors" should exist
    And the JSON nodes should contain:
      | authors[0].name                | Nareg Asmarian              |
      | authors[0].biography           | bioquil                     |
      | authors[0].role                | writer                      |
      | authors[0].jobtitle.name       | quality check               |
      | authors[0].jobtitle.qcode      | 1                           |
      | authors[0].twitter             | @superdeskman               |
      | authors[0].instagram           | superdeskman                |
      | authors[0].facebook            | superdeskman                |
      | authors[1].name                | vincer vincer               |
      | authors[1].biography           | not dead yet                |
      | authors[1].role                | subeditor                   |
    And the JSON node "authors[1].jobtitle.name" should not exist
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
              "is_published_fbia":false,
              "published":true
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
      | authors[0].name                | Nareg Asmarian              |
      | authors[0].biography           | bioquil                     |
      | authors[0].role                | writer                      |
      | authors[0].jobtitle.name       | quality check               |
      | authors[0].jobtitle.qcode      | 1                           |
      | authors[0].slug                | nareg-asmarian              |
      | authors[0].twitter             | @superdeskman               |
      | authors[0].instagram           | superdeskman                |
      | authors[0].facebook            | superdeskman                |
      | authors[1].name                | vincer vincer               |
      | authors[1].biography           | not dead yet                |
      | authors[1].role                | subeditor                   |
      | authors[1].slug                | vincer-vincer               |
    And the JSON node "authors[0].avatar" should be null

  Scenario: Submitting request payload containing authors data in ninjs format - updating
    Given I am authenticated as "test.user"
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
          "role":"writer",
          "avatar_url":"http://via.placeholder.com/90x90.jpg"
        },
        {
          "biography":"not dead yet",
          "name":"vincer vincer",
          "role":"subeditor",
          "avatar_url":"http://via.placeholder.com/95x95.jpg"
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
    And the JSON node "authors[0].avatar" should not be null
    And the JSON node "authors[1].avatar" should not be null
    And the JSON nodes should be equal to:
      | authors[0].name                | Nareg Asmarian                                   |
      | authors[0].biography           | bioquil                                          |
      | authors[0].role                | writer                                           |
      | authors[0].avatar_url          | http://localhost/author/media/nareg-asmarian_fd163b853e3b825257486222cb0f0cc08a6bb687.jpeg |
      | authors[0].jobtitle.name       | quality check                                    |
      | authors[0].jobtitle.qcode      | 1                                                |
      | authors[1].name                | vincer vincer                                    |
      | authors[1].biography           | not dead yet                                     |
      | authors[1].role                | subeditor                                        |
      | authors[1].avatar_url          | http://localhost/author/media/vincer-vincer_b92774bece43ddbdefe652cb7567e005cb66032c.jpeg  |
      | authors[0].slug                | nareg-asmarian                                   |
      | authors[1].slug                | vincer-vincer                                    |
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
    Then I send a "GET" request to "/api/v2/packages/6"
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
    Then I send a "GET" request to "/api/v2/content/articles/testing-authors"
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
          "biography":"ed bio",
          "name":"ed",
          "role":"subeditor"
        },
        {
          "biography":"not dead yet",
          "name":"vincer vincer",
          "role":"subeditor",
          "avatar_url":"http://via.placeholder.com/95x95.jpg"
        },
        {
          "biography":"ed bio",
          "name":"ed",
          "role":"maineditor"
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
    Then I send a "GET" request to "/api/v2/packages/6"
    Then the response status code should be 200
    And the JSON node "authors" should exist
    And the JSON node "authors[0].avatar" should not be null
    And the JSON node "authors[1].avatar" should be null
    And the JSON nodes should contain:
      | authors[1].name                | ed                                              |
      | authors[1].biography           | ed bio                                          |
      | authors[1].role                | maineditor                                       |
      | authors[0].name                | vincer vincer                                   |
      | authors[0].biography           | not dead yet                                    |
      | authors[0].role                | subeditor                                       |
      | authors[0].avatar_url          | http://localhost/author/media/vincer-vincer_b92774bece43ddbdefe652cb7567e005cb66032c.jpeg |
    And the JSON node "authors[1].jobtitle.name" should not exist
    And the JSON node "authors[1].avatar_url" should be null
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/testing-authors"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | authors[1].name                | ed                                              |
      | authors[1].biography           | ed bio                                          |
      | authors[1].role                | maineditor                                       |
      | authors[1].slug                | ed                                              |
      | authors[0].name                | vincer vincer                                   |
      | authors[0].biography           | not dead yet                                    |
      | authors[0].role                | subeditor                                       |
      | authors[0].avatar_url          | http://localhost/author/media/vincer-vincer_b92774bece43ddbdefe652cb7567e005cb66032c.jpeg |
      | authors[0].slug                | vincer-vincer                                   |
    And the JSON node "authors[1].avatar_url" should be null
    Then I send a "GET" request to "/author/media/vincer-vincer_b92774bece43ddbdefe652cb7567e005cb66032c.jpeg"
    Then the response status code should be 200
