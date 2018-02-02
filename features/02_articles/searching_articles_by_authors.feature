@articles
Feature: Filtering/searching existing articles by authors
  In order to pfilter articles by given author
  As a HTTP Client
  I want to be able to check if filtering works properly

  Scenario: Searching/filtering articles by authors
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I run "fos:elastica:populate --env=test" command
    Then I should see "Refreshing swp" in the output
    Then I send a "GET" request to "/api/{version}/content/articles/?author[]=Tom"
    Then the response status code should be 200
    And the JSON node "total" should be equal to "2"
