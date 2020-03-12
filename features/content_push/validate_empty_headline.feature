@content_push
Feature: Validating an incoming request payload when the headline is empty
  In order to publish an article
  As a HTTP Client
  I want to be able to check if the request payload is valid according to ninjs schema

  Scenario: Submitting request payload in ninjs format
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
        "language":"en","headline":"","version":"2","guid":"16e111d5","priority":6,"type":"text",
        "authors":[{"name":"Tom Doe","role":"editor"}],
        "byline":"ADmin"
    }
    """
    Then the response status code should be 500
    And the response should contain "None of the chained validators were able to validate the data!"
