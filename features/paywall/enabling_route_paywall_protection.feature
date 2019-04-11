@paywall
Feature: Enabling paywall-protection for routes
  In order to monetize the content per categories
  As a HTTP Client
  I want to enable paywall-protection for the routes

  Scenario: Enable route paywall-protection
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/routes/" with body:
     """
      {
          "name": "Politics",
          "slug": "politics",
          "type": "collection"
      }
    """
    Then the response status code should be 201
    And the JSON node "paywall_secured" should be false
    And the JSON node "id" should be equal to "7"

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/routes/7" with body:
     """
      {
          "paywall_secured": true
      }
     """
    Then the response status code should be 200
    And the JSON node "paywall_secured" should be true

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/routes/7"
    Then the response status code should be 200
    And the JSON node "paywall_secured" should be true
