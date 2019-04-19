@paywall
Feature: Marking article as paywall-secured using direct package publish
  In order to make article paywall-secured
  As a HTTP Client
  I want to be able to mark it immediately once the package is published

  Scenario: Mark article as paywall-secured on package publish
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
      "guid":"urn:newsml:sd-master.test.superdesk.org:2019-01-18T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc",
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
      "slugline":"lorem ipsum package",
      "headline":"lorem ipsum package",
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
              "is_published_fbia":false,
              "published":true,
              "paywall_secured":true
            }
          ]
      }
     """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/lorem-ipsum-package"
    Then the response status code should be 200
    And the JSON node "status" should be equal to "published"
    And the JSON node "paywall_secured" should be true
