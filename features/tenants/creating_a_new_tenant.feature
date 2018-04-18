Feature: Adding a new tenant
  In order to publish content to multiple channels
  As a HTTP Client
  I want to add a new tenant via API

  Scenario: Adding a new tenant
    Given I am authenticated as "test.user"
    When I send a "GET" request to "api/v1/tenants/"
    Then the response status code should be 200
    And the JSON node "_embedded._items" should have "3" elements
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/tenants/" with body:
     """
      {
        "tenant": {
          "domainName": "example2.com",
          "name": "Example tenant2",
          "subdomain": "tenant2"
        }
      }
    """
    Then the response status code should be 201
    Given I am authenticated as "test.user"
    When I send a "GET" request to "api/v1/tenants/"
    Then the response status code should be 200
    And the JSON node "_embedded._items" should have "4" elements

