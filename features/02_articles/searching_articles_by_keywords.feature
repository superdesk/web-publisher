@articles
Feature: Filtering/searching existing articles by keywords
  In order to see a list of articles filtered by keywords
  As a HTTP Client
  I want to be able to filter articles by given keywords

  Background:
    Given I run "fos:elastica:populate --env=test" command
    Then I should see "Refreshing swp" in the output

  Scenario: Searching/filtering articles by keywords
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?keywords[]=news"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 3

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?keywords[]=car"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 5
