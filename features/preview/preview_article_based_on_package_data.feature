@preview
Feature: Preview article under route based on package data

  Scenario: Preview article based on package under selected route
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Simple test route",
          "slug": "simple-test-route",
          "type": "collection",
          "articlesTemplateName": "article.html.twig"
      }
    """
    Then the response status code should be 201
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/preview/package/generate_token/7" with body:
    """
    {
      "language": "en",
      "byline":"John Jones",
      "source":"Sourcefabric",
      "type":"text",
      "description_text":"Lorem ipsum abstract another one",
      "guid":"urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bp",
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
          "biography":"bioquil edit",
          "name":"Nareg Asmarian",
          "jobtitle":{
            "qcode":"1",
            "name":"quality check"
          },
          "role":"writer"
        }
      ],
      "copyrightholder":"",
      "slugline":"package preview test",
      "headline":"package preview test",
      "version":"3",
      "description_html":"<p>Lorem ipsum abstract</p>",
      "located":"Sydney",
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
      "firstcreated":"2018-01-19T09:26:52+0000"
	}
    """
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    {
        "preview_url": "http:\/\/localhost\/preview\/publish\/package\/0123456789abc"
    }
    """
    When I go to "http://localhost/preview/publish/package/0123456789abc"
    Then the response status code should be 200
    And the response should contain "package preview test"
    And the response should contain "Name: Nareg Asmarian"
    And the response should contain "Slug: nareg-asmarian"
    And the response should contain "Biography: bioquil edit"
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/preview/package/generate_token/7" with body:
    """
    {
      "language": "en",
      "byline":"John Jones",
      "source":"Sourcefabric",
      "type":"text",
      "description_text":"Lorem ipsum abstract another one",
      "guid":"urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bp",
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
          "biography":"bioquil edit",
          "name":"Nareg Asmarian",
          "jobtitle":{
            "qcode":"1",
            "name":"quality check"
          },
          "role":"writer"
        }
      ],
      "copyrightholder":"",
      "slugline":"package preview test edit",
      "headline":"package preview test edit",
      "version":"3",
      "description_html":"<p>Lorem ipsum abstract</p>",
      "located":"Sydney",
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
      "firstcreated":"2018-01-19T09:26:52+0000"
	}
    """
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    {
        "preview_url": "http:\/\/localhost\/preview\/publish\/package\/0123456789abc"
    }
    """
    When I go to "http://localhost/preview/publish/package/0123456789abc"
    Then the response status code should be 200
    And the response should contain "package preview test edit"
