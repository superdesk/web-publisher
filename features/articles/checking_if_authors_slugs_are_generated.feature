@articles
Feature: Checking author slugs generation
  In order to filter/list articles by author slug
  As a HTTP Client
  I want to be able to check if author slug is generated from name properly

  Scenario: Submitting content with authors
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I run "fos:elastica:populate --env=test" command
    Then I should see "Refreshing swp" in the output
    Then I send a "GET" request to "/api/v2/content/articles/?author[]=1"
    Then the response status code should be 200
    And the JSON node "total" should be equal to "1"
