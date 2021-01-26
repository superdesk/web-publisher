@webhooks
Feature: Manage Webhooks
  In order to work with webhooks
  As a HTTP Client
  I want to be able to manage them by API

  Scenario: Listing existing webhooks
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a GET request to "/api/v2/webhooks/"
    Then the response status code should be 200
    And the JSON node total should be equal to 0

  Scenario: Creating new webhook
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/webhooks/" with body:
     """
      {
          "url": "https://example.com",
          "events": [
            "article[published]"
          ],
          "enabled": "1"
      }
    """
    Then the response status code should be 201

  Scenario: Listing existing webhooks after creating one
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a GET request to "/api/v2/webhooks/"
    Then the response status code should be 200
    And the JSON node total should be equal to 1
    And the JSON node "_embedded._items[0].url" should be equal to "https://example.com"
    And the JSON node "_embedded._items[0].events[0]" should be equal to "article[published]"
    And the JSON node "_embedded._items[0].enabled" should be equal to true

  Scenario: Updating existing webhook:
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/webhooks/1" with body:
     """
      {
          "url": "https://example2.com",
          "events": [
              "article[updated]"
          ],
          "enabled": "0"
      }
    """
    Then  the response status code should be 200

  Scenario: Fetching single webhook after update
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a GET request to "/api/v2/webhooks/1"
    Then the response status code should be 200
    And the JSON node "url" should be equal to "https://example2.com"
    And the JSON node "events[0]" should be equal to "article[updated]"
    And the JSON node "enabled" should be false


  Scenario: Deleting webhook
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a DELETE request to "/api/v2/webhooks/1"
    Then the response status code should be 204

  Scenario: Listing existing webhooks after deleting one
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a GET request to "/api/v2/webhooks/"
    Then the response status code should be 200
    And the JSON node total should be equal to 0
