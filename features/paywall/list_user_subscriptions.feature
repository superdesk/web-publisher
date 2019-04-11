@paywall
Feature: List subscriptions by user
  In order to display a list of user's subscriptions
  As a HTTP Client
  I want to enable return a list of subscriptions by user

  Scenario: List subscriptions by user
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/subscriptions/1"
    Then the response status code should be 200
    And the JSON node "[0].id" should be equal to "79"
    And the JSON node "[0].type" should be equal to "recurring"
    And the JSON node "[0].details.intention" should be equal to "bottom_box"
    And the JSON node "[0].details.source" should be equal to "web_version"
    And the JSON node "[0].updated_at" should be null
    And the JSON node "[0].created_at" should not be null

  Scenario: List subscriptions by user and article id
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/subscriptions/1?articleId=20"
    Then the response status code should be 200
    And the JSON node "[0].id" should be equal to "12"
    And the JSON node "[0].type" should be equal to "recurring"
    And the JSON node "[0].details.intention" should be equal to "bottom_box"
    And the JSON node "[0].details.source" should be equal to "web_version"
    And the JSON node "[0].details.articleId" should be equal to "20"
    And the JSON node "[0].updated_at" should be null
    And the JSON node "[0].created_at" should not be null

  Scenario: List subscriptions by user and article id and route id
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/subscriptions/1?articleId=20&routeId=10"
    Then the response status code should be 200
    And the JSON node "[0].id" should be equal to "14"
    And the JSON node "[0].type" should be equal to "recurring"
    And the JSON node "[0].details.intention" should be equal to "bottom_box"
    And the JSON node "[0].details.source" should be equal to "web_version"
    And the JSON node "[0].details.articleId" should be equal to "20"
    And the JSON node "[0].details.routeId" should be equal to "10"
    And the JSON node "[0].updated_at" should be null
    And the JSON node "[0].created_at" should not be null