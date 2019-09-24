@articles
Feature: Collect article statistics
  In order to work with article statistics
  As a HTTP Client
  I want to be able to count page views and see them in API

  Scenario: Opening article page
    When I go to "/news/test-news-article"
    Then the response status code should be 200
    And I should see "/_swp_analytics?articleId=1'+request_randomizer+'&ref='+document.referrer"

  Scenario: Send analytics request
    When I go to "/_swp_analytics?articleId=1"
    Then the response status code should be 200
    And the header "terminate-immediately" should be equal to "1"


  Scenario: Send analytics request
    When I go to "/_swp_analytics?articleId=1&sdrybretybr5yrd&&ref=http://localhost/"
    Then the response status code should be 200
    And the header "terminate-immediately" should be equal to "1"

  Scenario: Check article statistics
    When I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/test-news-article"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | slug                                   | test-news-article |
      | article_statistics.page_views_number   | 22                |
