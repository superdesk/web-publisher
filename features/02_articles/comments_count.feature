@articles
Feature: Setting comments count in articles
  In order store comments count in articles
  As a HTTP Client
  I want to be able to push JSON content with data and set it as comments count

  Scenario: Submitting request payload in json format
    When I run "swp:organization:update Organization1 --env=test --secretToken secret_token" command
    Then I should see "Organization Organization1 (code: 123456, secret token: secret_token) has been updated and is enabled!" in the output
    When I add "Content-Type" header equal to "application/json"
    And I add "x-publisher-signature" header equal to "sha1=0dcd1953d72dda47f4a4acedfd638a3c58def7bc"
    And I wait 3 seconds
    Then I send a "PATCH" request to "/api/v1/content/articles" with body:
     """
      {
          "url": "http://localhost/news/test-news-article",
          "comments_count": 31
      }
     """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | comments_count | 31 |
    And the JSON node "created_at" should be equal to "updated_at" node
