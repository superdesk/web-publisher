@authors
Feature: Searching authors
  Scenario: Searching authors by search phrase
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    And I run "fos:elastica:populate --env=test" command
    Then I should see "Refreshing swp" in the output
    Then I send a "GET" request to "/api/v2/authors/?term=Tom"
    Then the response status code should be 200
    And the JSON node "total" should be equal to "1"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/authors/?term=John Doe"
    Then the response status code should be 200
    And the JSON node "total" should be equal to "2"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/authors/?term=John Doe Second"
    Then the response status code should be 200
    And the JSON node "total" should be equal to "1"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/authors/?term=John"
    Then the response status code should be 200
    And the JSON node "total" should be equal to "2"
