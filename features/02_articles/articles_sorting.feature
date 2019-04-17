@articles
Feature: List sorted articles
  In order to see if sorting by underscore, camelCase properties works
  As a HTTP Client
  I want to be able to sort articles by underscore and camelCase properties

  Scenario: Sort articles by underscore property
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[updated_at]=desc"
    Then the response status code should be 200

  Scenario: Sort articles by camelCase property
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[updatedAt]=desc"
    Then the response status code should be 200

  Scenario: Sort articles by fake property
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[fake]=desc"
    Then the response status code should be 500
