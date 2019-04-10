@paywall
Feature: Enabling paywall-protection for articles
  In order to monetize the content by selling access to single articles
  As a HTTP Client
  I want to enable paywall-protection for the article

  Scenario: Enable article paywall-protection
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/features"
    Then the response status code should be 200
    And the JSON node "slug" should be equal to "features"
    And the JSON node "paywallSecured" should be false

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v1/content/articles/features" with body:
     """
      {
          "paywallSecured": true
      }
     """
    Then the response status code should be 200
    And the JSON node "paywallSecured" should be true

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/articles/features"
    Then the response status code should be 200
    And the JSON node "paywallSecured" should be true
