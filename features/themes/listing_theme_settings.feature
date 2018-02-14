@theme
Feature: Checking if theme settings work correctly
  In order to manage theme settings
  As a HTTP Client
  I want to be able to read theme settings via API

  Scenario: Listing theme settings
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/theme/settings/"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | primary_font_family.value      | Roboto                      |
      | primary_font_family.scope      | theme                       |
      | primary_font_family.label      | Primary Font Family         |
      | primary_font_family.type       | string                      |
      | secondary_font_family.value    | Roboto                      |
      | secondary_font_family.scope    | theme                       |
      | secondary_font_family.label    | Secondary Font Family       |
      | secondary_font_family.type     | string                      |
      | body_font_size.value           | 14                          |
      | body_font_size.scope           | theme                       |
      | body_font_size.label           | Body Font Size              |
      | body_font_size.type            | integer                     |
    Then the JSON node "instance_name" should not exist
