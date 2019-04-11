@packages
Feature: Working with package external data
  In order store extra data for package from external systems
  As a HTTP Client
  I want to be able to push JSON content with data and assign it to package

  Scenario: Submitting request payload in json format
    When I run "swp:organization:update Organization1 --env=test --secretToken secret_token" command
    Then I should see "Organization Organization1 (code: 123456, secret token: secret_token) has been updated and is enabled!" in the output
    When I add "Content-Type" header equal to "application/json"
    And I add "x-publisher-signature" header equal to "sha1=0dcd1953d72dda47f4a4acedfd638a3c58def7bc"
    And I send a "PUT" request to "/api/v2/packages/extra/test-news-article" with body:
    """
    {
      "example_key": "example data",
      "some other key": "some other value"
    }
    """
    Then the response status code should be 200

    When I add "Content-Type" header equal to "application/json"
    Given I am authenticated as "test.user"
    And I send a "GET" request to "/api/v2/packages/extra/test-news-article"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | example_key    | example data     |
      | some other key | some other value |

    When I add "Content-Type" header equal to "application/json"
    And I add "x-publisher-signature" header equal to "sha1=33f10018ff3dae1669f6dcb733b63c8b503ceb26"
    And I send a "PUT" request to "/api/v2/packages/extra/test-news-article" with body:
    """
    {}
    """
    Then the response status code should be 200
    And the JSON node example_key should not exist

    When I add "Content-Type" header equal to "application/json"
    And I add "x-publisher-signature" header equal to "sha1=5c366d2a68d62ddddaeba5261ca4a4a22458934c"
    And I send a "PUT" request to "/api/v2/packages/extra/demo-not-exisiting-slug" with body:
    """
    {
      "example_key": "example data"
    }
    """
    Then the response status code should be 404

    When I add "Content-Type" header equal to "application/json"
    Given I am authenticated as "test.user"
    And I send a "GET" request to "/api/v2/packages/extra/demo-not-exisiting-slug"
    Then the response status code should be 404

    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/packages/extra/test-news-article"
    Then the response status code should be 401

    When I add "Content-Type" header equal to "application/json"
    And I add "x-publisher-signature" header equal to "sha1=wrongtoken"
    And I send a "PUT" request to "/api/v2/packages/extra/test-news-article" with body:
    """
    {
      "example_key": "example data"
    }
    """
    Then the response status code should be 401
