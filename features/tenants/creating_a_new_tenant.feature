Feature: Adding a new tenant
  In order to publish content to multiple channels
  As a HTTP Client
  I want to add a new tenant via API

  Scenario: Adding a new tenant
    Given I am authenticated as "test.user"
    When I send a "GET" request to "api/v2/tenants/"
    Then the response status code should be 200
    And the JSON node "_embedded._items" should have "3" elements
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/tenants/" with body:
     """
      {
          "domainName": "example2.com",
          "name": "Example tenant2",
          "subdomain": "tenant2"
      }
    """
    Then the response status code should be 201
    And the JSON node "code" should exist
    And we save it into "tenant_code"
    Given I am authenticated as "test.user"
    When I send a "GET" request to "api/v2/tenants/"
    Then the response status code should be 200
    And the JSON node "_embedded._items" should have "4" elements

    Given I am authenticated as "test.user"
    And I send a "GET" request to "api/v2/tenants/<<tenant_code>>"
    Then the response status code should be 200
    And the JSON node "id" should be equal to 4
    And the JSON node "code" should be equal to "<<tenant_code>>"

  Scenario: Checking if domain/subdoman is handled by Publisher
    When I send a "GET" request to "http://testdoman.localhost"
    Then the response status code should be 404
    And the header "X-Superdesk-Publisher" should be equal to "1-test"
    When I send a "GET" request to "http://localhost"
    Then the response status code should be 200
    And the header "X-Superdesk-Publisher" should be equal to "1-test"
