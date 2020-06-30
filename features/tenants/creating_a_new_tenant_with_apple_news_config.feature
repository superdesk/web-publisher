Feature: Adding a new tenant with Apple News config
  In order to publish content to multiple channels
  As a HTTP Client
  I want to add a new tenant with Apple News config via API

  Scenario: Adding a new tenant with Apple News integration
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/tenants/" with body:
     """
      {
          "domain_name": "example.com",
          "name": "Example tenant",
          "subdomain": "tenant1",
          "apple_news_config": {
            "channel_id": "channelId",
            "api_key_id": "apiKeyId",
            "api_key_secret": "apiKeySecret"
          }
      }
    """
    Then the response status code should be 201
    Then the JSON node "code" should exist
    And we save it into "tenant_code"
    And the JSON node "apple_news_config.channel_id" should be equal to "channelId"
    And the JSON node "apple_news_config.api_key_id" should be equal to "apiKeyId"
    And the JSON node "apple_news_config.api_key_secret" should be equal to "apiKeySecret"

    Given I am authenticated as "test.user"
    And I send a "GET" request to "/api/v2/tenants/"
    And the JSON node "_embedded._items[3].apple_news_config.channel_id" should be equal to "channelId"
    And the JSON node "_embedded._items[3].apple_news_config.api_key_id" should be equal to "apiKeyId"
    And the JSON node "_embedded._items[3].apple_news_config.api_key_secret" should be equal to "apiKeySecret"

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/tenants/<<tenant_code>>" with body:
     """
      {
          "apple_news_config": {
            "channel_id": null,
            "api_key_id": null,
            "api_key_secret": null
          }
      }
    """
    Then the response status code should be 200
    And the JSON node "apple_news_config.channel_id" should be null
    And the JSON node "apple_news_config.api_key_id" should be null
    And the JSON node "apple_news_config.api_key_secret" should be null
