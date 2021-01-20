@user
@disable-fixtures
Feature: Checking if user settings works correctly
  In order to manage user settings
  As a HTTP Client
  I want to be able to read settings via API

  Scenario: Listing all settings by different scopes
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      |  code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc  |

    Given the following Users:
      | username   | email                        | token      | password | role        |
      | test.reader  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_USER   |

    Given I am authenticated as "test.reader"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/users/settings/"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    [
        {
            "scope": "user",
            "type": "string",
            "value": "{}",
            "name": "filtering_prefrences"
        },
        {
            "scope": "user",
            "type": "string",
            "value": "{}",
            "name": "user_private_preferences"
        },
        {
            "scope": "user",
            "type": "string",
            "value": "{}",
            "name": "user_favourite_articles"
        },
        {
            "scope": "user",
            "value": "sdfgesgts4tgse5tdg4t",
            "type": "string",
            "name": "third_setting"
        }
    ]
    """

    Then I am authenticated as "test.reader"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/users/settings/" with body:
    """
    {
      "name": "user_private_preferences",
      "value": "somecontent"
    }
    """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | name  | user_private_preferences |
      | scope | user                     |
      | value | somecontent              |

    Given I am authenticated as "test.reader"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/users/settings/"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    [
        {
            "scope": "user",
            "type": "string",
            "value": "{}",
            "name": "filtering_prefrences"
        },
        {
            "scope": "user",
            "type": "string",
            "value": "somecontent",
            "name": "user_private_preferences"
        },
        {
            "scope": "user",
            "type": "string",
            "value": "{}",
            "name": "user_favourite_articles"
        },
        {
            "scope": "user",
            "value": "sdfgesgts4tgse5tdg4t",
            "type": "string",
            "name": "third_setting"
        }
    ]
    """
