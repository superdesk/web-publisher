@articles
Feature: Redirect to article page by its slug

  Scenario: Redirect to article page by slug
    When I send a GET request to "/r/test-news-article"
    Then the response status code should be 301
    And I follow the redirection
    Then the response status code should be 200
    When I go to "/r/test-news-article"
    Then the response status code should be 200
