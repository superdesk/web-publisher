Feature: Adding a new tenant with PWA config
  In order to publish content to PWA
  As a HTTP Client
  I want to add a new tenant with PWA config via API

  Scenario: Adding a new tenant with PWA integration
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/tenants/" with body:
     """
      {
          "domain_name": "example.com",
          "name": "Example tenant",
          "subdomain": "tenant1",
          "pwa_config": {
            "url": "http://pwaurl.local"
          }
      }
    """
    Then the response status code should be 201
    Then the JSON node "code" should exist
    And we save it into "tenant_code"
    And the JSON node "pwa_config.url" should be equal to "http://pwaurl.local"

    Given I am authenticated as "test.user"
    And I send a "GET" request to "/api/v2/tenants/"
    And the JSON node "_embedded._items[3].pwa_config.url" should be equal to "http://pwaurl.local"

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/tenants/<<tenant_code>>" with body:
     """
      {
          "pwa_config": {
            "url": null
          }
      }
    """
    Then the response status code should be 200
    And the JSON node "pwa_config.url" should be null

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/tenants/<<tenant_code>>" with body:
     """
      {
          "pwa_config": {
            "url": "not_valid_url"
          }
      }
    """
    Then the response status code should be 400
    And the JSON should be equal to:
    """
      {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
          "children": {
            "name": {},
            "subdomain": {},
            "domainName": {},
            "themeName": {},
            "organization": {},
            "ampEnabled": {},
            "fbiaEnabled": {},
            "paywallEnabled": {},
            "outputChannel": {
              "children": {
                "type": {}
              }
            },
            "defaultLanguage": {},
            "appleNewsConfig": {
              "children": {
                "channelId": {},
                "apiKeyId": {},
                "apiKeySecret": {}
              }
            },
            "pwaConfig": {
              "children": {
                "url": {
                  "errors": [
                    "The PWA url \"\"not_valid_url\"\" is not a valid url."
                  ]
                }
              }
            }
          }
        }
      }
    """
