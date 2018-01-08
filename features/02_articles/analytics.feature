Feature: Collect article statistics
  In order to work with article statistics
  As a HTTP Client
  I want to be able to count page views and see them in API

  Scenario: Opening article page
    When I go to "/news/test-news-article"
    Then the response status code should be 200
    And I should see "/_swp_analytics?articleId=1"

  Scenario: Send analytics request
    When I go to "/_swp_analytics?articleId=1"
    Then the response status code should be 200
    And the header "terminate-imidediately" should be equal to "1"

