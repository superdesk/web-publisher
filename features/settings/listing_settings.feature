@packages
Feature: Checking if created package is returned properly by api
  In order to package
  As a HTTP Client
  I want to be able to push JSON content with package and see it in the system

  Scenario: Listing theme settings
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
    Then the JSON node "primary_font_family" should exist
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
    #"primary_font_family":{"label":"Primary Font Family","value":"Roboto","type":"string","help":"The primary font","scope":"theme"},"secondary_font_family":{"value":"Roboto","type":"string","scope":"theme"},"body_font_size":{"label":"Body Font Size","value":14,"type":"integer","scope":"theme"}}