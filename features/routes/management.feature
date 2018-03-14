@routes.management
Feature: Manage Routes
  In order to work with routes
  As a HTTP Client
  I want to be able to manage them by API

  Scenario: Listing existing routes
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a GET request to "/api/v1/content/routes/"
    Then the response status code should be 200
    And the JSON node total should be equal to 5

  Scenario: Creating new route with custom slug
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route": {
          "name": "Simple test route",
          "slug": "simple-test-route",
          "type": "collection",
          "content": null
        }
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Simple test route"
    And the JSON node slug should be equal to "simple-test-route"
    And the JSON node staticPrefix should be equal to "/simple-test-route"

  Scenario: Creating new route without custom slug
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route": {
          "name": "Simple test route number 2",
          "type": "collection",
          "content": null
        }
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Simple test route number 2"
    And the JSON node slug should be equal to "simple-test-route-number-2"
    And the JSON node staticPrefix should be equal to "/simple-test-route-number-2"

  Scenario: Creating new route without type custom
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route": {
          "name": "Custom route",
          "slug": "custom-route",
          "type": "custom",
          "variablePattern": "/{customParameter}",
          "requirements": [
            {
              "key": "customParameter",
              "value": "[a-zA-Z]+"
            }
		  ]
        }
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Custom route"
    And the JSON node slug should be equal to "custom-route"
    When I send a "GET" request to "/custom-route/parameterName"
    Then the response status code should be 200
    When I send a "GET" request to "/custom-route/parameterName234"
    Then the response status code should be 404