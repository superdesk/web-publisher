@theme
Feature: Listing updated theme settings for two different tenants

  Scenario: Listing updated theme settings
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/" with body:
    """
    {
        "name":"primary_font_family",
        "value":"Oswald"
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/theme/settings/"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    [
      {
        "value":"",
        "scope":"theme",
        "type":"string",
        "name":"theme_logo"
      },
      {
        "value":"",
        "scope":"theme",
        "type":"string",
        "name":"theme_logo_second"
      },
      {
        "value":"",
        "scope":"theme",
        "type":"string",
        "name":"theme_logo_third"
      },
      {
        "label":"Primary Font Family",
        "value":"Oswald",
        "type":"string",
        "options":[
          {
            "value":"Roboto",
            "label":"Roboto"
          },
          {
            "value":"Lato",
            "label":"Lato"
          },
          {
            "value":"Oswald",
            "label":"Oswald"
          }
        ],
        "scope":"theme",
        "name":"primary_font_family"
      },
      {
        "label":"Secondary Font Family",
        "value":"Roboto",
        "type":"string",
        "options":[
          {
            "value":"Roboto",
            "label":"Roboto"
          },
          {
            "value":"Lato",
            "label":"Lato"
          },
          {
            "value":"Oswald",
            "label":"Oswald"
          }
        ],
        "scope":"theme",
        "name":"secondary_font_family"
      },
      {
        "label":"Body Font Size",
        "value":14,
        "type":"integer",
        "scope":"theme",
        "name":"body_font_size"
      },
      {
        "label": "Simple switch",
        "value": false,
        "type": "boolean",
        "scope": "theme",
        "name": "switch"
      }
    ]
    """
    And I am authenticated as "test.client2"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "http://client2.localhost/api/v2/settings/" with body:
    """
    {
        "name":"body_font_size",
        "value":"16px"
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.client2"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "http://client2.localhost/api/v2/theme/settings/"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    [
      {
        "value":"",
        "scope":"theme",
        "type":"string",
        "name":"theme_logo"
      },
      {
        "value":"",
        "scope":"theme",
        "type":"string",
        "name":"theme_logo_second"
      },
      {
        "value":"",
        "scope":"theme",
        "type":"string",
        "name":"theme_logo_third"
      },
      {
        "label":"Primary Font Family",
        "value":"Roboto",
        "type":"string",
        "options":[
          {
            "value":"Roboto",
            "label":"Roboto"
          },
          {
            "value":"Lato",
            "label":"Lato"
          },
          {
            "value":"Oswald",
            "label":"Oswald"
          }
        ],
        "scope":"theme",
        "name":"primary_font_family"
      },
      {
        "label":"Body Font Size",
        "value":"16px",
        "type":"string",
        "scope":"theme",
        "name":"body_font_size"
      }
    ]
    """
