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
          "name": "Simple test route",
          "slug": "simple-test-route",
          "type": "collection",
          "content": null
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
          "name": "Simple test route number 2",
          "type": "collection",
          "content": null
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Simple test route number 2"
    And the JSON node slug should be equal to "simple-test-route-number-2"
    And the JSON node staticPrefix should be equal to "/simple-test-route-number-2"


  Scenario: Creating new route with parent and un-setting parent
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Simple test root route",
          "type": "content"
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Simple test root route"
    And the JSON node level should be equal to 0

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Simple test child route",
          "type": "content",
          "parent": 9
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Simple test child route"
    And the JSON node level should be equal to 1

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/10" with body:
     """
      {
          "parent": null
      }
    """
    Then the response status code should be 200
    And the JSON node name should be equal to "Simple test child route"
    And the JSON node level should be equal to 0

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v1/content/routes/10"
    Then the response status code should be 200
    And the JSON node name should be equal to "Simple test child route"
    And the JSON node level should be equal to 0
#
  Scenario: Creating new route without type custom
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
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
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Custom route"
    And the JSON node slug should be equal to "custom-route"
    When I send a "GET" request to "/custom-route/parameterName"
    Then the response status code should be 200
    When I send a "GET" request to "/custom-route/parameterName234"
    Then the response status code should be 404

  Scenario: Changing route position (order)
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/11" with body:
     """
      {
          "position": 6
      }
    """
    Then the response status code should be 200
    And the JSON node position should be equal to 6
    And the JSON node lft should be equal to 15
    And the JSON node rgt should be equal to 16

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/11" with body:
     """
      {
          "position": 0
      }
    """
    Then the response status code should be 200
    And the JSON node position should be equal to 0
    And the JSON node lft should be equal to 1
    And the JSON node rgt should be equal to 2

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/11" with body:
     """
      {
          "position": 9
      }
    """
    Then the response status code should be 200
    And the JSON node position should be equal to 8
    And the JSON node lft should be equal to 19
    And the JSON node rgt should be equal to 20
    And the JSON node level should be equal to 0

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/11" with body:
     """
      {
          "parent": 3,
          "position": 1
      }
    """
    Then the response status code should be 200
    And the JSON node parent should be equal to 3
    And the JSON node position should be equal to 1
    And the JSON node lft should be equal to 6
    And the JSON node rgt should be equal to 7
    And the JSON node level should be equal to 1

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/11" with body:
     """
      {
          "parent": 3,
          "position": 4
      }
    """
    Then the response status code should be 200
    And the JSON node parent should be equal to 3
    And the JSON node position should be equal to 1
    And the JSON node lft should be equal to 6
    And the JSON node rgt should be equal to 7

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/11" with body:
     """
      {
          "position": 0
      }
    """
    Then the response status code should be 200
    And the JSON node parent should be equal to 3
    And the JSON node position should be equal to 0
    And the JSON node lft should be equal to 4
    And the JSON node rgt should be equal to 5
    And the JSON node level should be equal to 1

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v1/content/routes/11" with body:
     """
      {
          "position": -1
      }
    """
    Then the response status code should be 400
