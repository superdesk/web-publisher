@packages
Feature: Checking if created package is returned properly by api
  In order to package
  As a HTTP Client
  I want to be able to push JSON content with package and see it in the system

  Scenario: Submitting request payload in ninjs format
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
    And the JSON node "updated_at" should exist
    And the JSON node "created_at" should exist
    And the JSON node "extra" should exist
    And the JSON node "extra.custom-date" should be equal to "2018-01-18T00:00:00+0000"
    And the JSON node "extra.ID" should be equal to "<p>custom botttom field text</p>"
    And the JSON node "extra.limit-test" should be equal to "<p>limit test field</p>"
