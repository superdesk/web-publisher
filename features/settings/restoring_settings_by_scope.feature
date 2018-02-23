@settings
Feature: Checking if restoring settings by scope works correctly
  In order to restore settings
  As a HTTP Client
  I want to be able to see if the settings where restored properly

  Scenario: Restoring settings to defaults by scope
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
    And the JSON nodes should contain:
      | primary_font_family.value               | Roboto                      |
      | primary_font_family.scope               | theme                       |
      | primary_font_family.label               | Primary Font Family         |
      | primary_font_family.type                | string                      |
      | secondary_font_family.value             | Roboto                      |
      | secondary_font_family.scope             | theme                       |
      | secondary_font_family.label             | Secondary Font Family       |
      | secondary_font_family.type              | string                      |
      | secondary_font_family.options[0].value  | Roboto                      |
      | secondary_font_family.options[0].label  | Roboto                      |
      | secondary_font_family.options[1].value  | Lato                        |
      | secondary_font_family.options[1].label  | Lato                        |
      | secondary_font_family.options[2].value  | Oswald                      |
      | secondary_font_family.options[2].label  | Oswald                      |
      | body_font_size.value                    | 14                          |
      | body_font_size.scope                    | theme                       |
      | body_font_size.label                    | Body Font Size              |
      | body_font_size.type                     | integer                     |
      | instance_name.scope                     | global                      |
      | instance_name.value                     | Publisher Master            |
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/{version}/settings/" with body:
    """
    {
      "settings": {
        "name":"instance_name",
        "value":"Publisher edited"
      }
    }
    """
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/{version}/settings/" with body:
    """
    {
      "settings": {
        "name":"primary_font_family",
        "value":"Oswald"
      }
    }
    """
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/{version}/settings/" with body:
    """
      {
        "settings": {
          "name":"secondary_font_family",
          "value":"Lato"
        }
      }
    """
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
    Then the JSON node "primary_font_family" should exist
    And the JSON nodes should contain:
      | primary_font_family.value               | Oswald                      |
      | primary_font_family.scope               | theme                       |
      | primary_font_family.label               | Primary Font Family         |
      | primary_font_family.type                | string                      |
      | secondary_font_family.value             | Lato                        |
      | secondary_font_family.scope             | theme                       |
      | secondary_font_family.label             | Secondary Font Family       |
      | secondary_font_family.type              | string                      |
      | secondary_font_family.options[0].value  | Roboto                      |
      | secondary_font_family.options[0].label  | Roboto                      |
      | secondary_font_family.options[1].value  | Lato                        |
      | secondary_font_family.options[1].label  | Lato                        |
      | secondary_font_family.options[2].value  | Oswald                      |
      | secondary_font_family.options[2].label  | Oswald                      |
      | body_font_size.value                    | 14                          |
      | body_font_size.scope                    | theme                       |
      | body_font_size.label                    | Body Font Size              |
      | body_font_size.type                     | integer                     |
      | instance_name.scope                     | global                      |
      | instance_name.value                     | Publisher edited            |
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/settings/revert/theme"
    Then the response status code should be 204
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
    Then the JSON node "primary_font_family" should exist
    And the JSON nodes should contain:
      | primary_font_family.value               | Roboto                      |
      | primary_font_family.scope               | theme                       |
      | primary_font_family.label               | Primary Font Family         |
      | primary_font_family.type                | string                      |
      | secondary_font_family.value             | Roboto                      |
      | secondary_font_family.scope             | theme                       |
      | secondary_font_family.label             | Secondary Font Family       |
      | secondary_font_family.type              | string                      |
      | secondary_font_family.options[0].value  | Roboto                      |
      | secondary_font_family.options[0].label  | Roboto                      |
      | secondary_font_family.options[1].value  | Lato                        |
      | secondary_font_family.options[1].label  | Lato                        |
      | secondary_font_family.options[2].value  | Oswald                      |
      | secondary_font_family.options[2].label  | Oswald                      |
      | body_font_size.value                    | 14                          |
      | body_font_size.scope                    | theme                       |
      | body_font_size.label                    | Body Font Size              |
      | body_font_size.type                     | integer                     |
      | instance_name.scope                     | global                      |
      | instance_name.value                     | Publisher edited            |
    Then the response status code should be 200
