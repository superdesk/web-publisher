Feature: Adding a new tenant with output channel
  In order to publish content to multiple channels
  As a HTTP Client
  I want to add a new tenant via API

  Scenario: Adding a new tenant with Wordpress output channel
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/tenants/" with body:
     """
      {
        "tenant": {
          "domainName": "example.com",
          "name": "Example tenant",
          "subdomain": "tenant1",
          "outputChannel": {
            "type": "wordpress",
            "config": {
              "url": "api.wordpress.com",
              "key": "private key",
              "secret": "secret"
            }
          }
        }
      }
    """
    Then the response status code should be 201
