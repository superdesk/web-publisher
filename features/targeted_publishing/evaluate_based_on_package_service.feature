@targeted_publishing
Feature: Evaluate on package services

  Scenario: Evaluate package services
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v1/organization/rules/" with body:
     """
      {
        "rule":{
          "name":"Test rule",
          "description":"Test rule description",
          "priority":1,
          "expression":"package.getLanguage() == \"en\"",
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
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v1/rules/" with body:
     """
      {
        "rule":{
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"\"Politik\" in article.getPackage().getServicesNames()",
          "configuration":[
            {
              "key":"route",
              "value":6
            },
            {
              "key":"published",
              "value":true
            }
          ]
        }
      }
     """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/{version}/organization/rules/evaluate" with body:
     """
     {
        "guid":"urn:newsml:handelsblatt-api.superdesk.pro:2018-06-15T12:57:20.174037:e8cacf9e-e0ca-4b95-b915-84f274935df5",
        "language":"en",
        "body_html":"<p>testing targeted publishing</p>",
        "headline":"testing targeted publishing",
        "description_html":"<p>testing targeted publishing</p>",
        "pubstatus":"usable",
        "source":"hb",
        "service":[
          {
            "name":"Politik"
          }
        ]
     }
     """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenants[0].tenant.code  | 123abc |
      | tenants[0].route.id     | 6      |
