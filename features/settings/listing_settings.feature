@settings
Feature: Checking if settings work correctly
  In order to manage settings
  As a HTTP Client
  I want to be able to read settings via API by different scopes

  Scenario: Listing all settings by different scopes
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
    Then the response status code should be 200
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
      | instance_name.value                     | Publisher Master            |
      | instance_name.scope                     | global                      |
