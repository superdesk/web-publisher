@settings
Feature: Checking if restoring settings by scope works correctly
  In order to restore settings
  As a HTTP Client
  I want to be able to see if the settings where restored properly

  Scenario: Restoring settings to defaults by scope
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    And the JSON should be equal to:
    """
    [
      {
        "type":"boolean",
        "scope":"tenant",
        "value":true,
        "name":"registration_enabled"
      },
      {
        "value":"@FOSUser\/Registration\/email.txt.twig",
        "scope":"tenant",
        "type":"string",
        "name":"registration_confirmation.template"
      },
      {
        "value":{
          "contact@publisher.test":"Publisher"
        },
        "scope":"tenant",
        "type":"array",
        "name":"registration_from_email.confirmation"
      },
      {
        "value":"@FOSUser\/Resetting\/email.txt.twig",
        "scope":"tenant",
        "type":"string",
        "name":"registration_resetting.template"
      },
      {
        "value":{
          "contact@publisher.test":"Publisher"
        },
        "scope":"tenant",
        "type":"array",
        "name":"registration_from_email.resetting"
      },
      {
        "scope":"global",
        "type":"string",
        "value":"Publisher Master",
        "name":"instance_name"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"filtering_prefrences"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"user_private_preferences"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"user_favourite_articles"
      },
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
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"use_first_published_as_publish_date"
      },
      {
        "type": "boolean",
        "scope": "tenant",
        "value": false,
        "name": "override_slug_on_correction"
      },
      {
        "type": "string",
        "scope": "tenant",
        "value": "(Photo: {{ author }})",
        "name": "embedded_image_author_template"
      },
      {
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"fbia_enabled"
      },
      {
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"paywall_enabled"
      },
      {
          "type": "string",
          "scope": "tenant",
          "value": "en",
          "name": "default_language"
      },
      {
          "scope": "global",
          "value": null,
          "type": "string",
          "name": "first_setting"
      },
      {
          "scope": "global",
          "value": 123,
          "type": "string",
          "name": "second_setting"
      },
      {
          "scope": "user",
          "value": "sdfgesgts4tgse5tdg4t",
          "type": "string",
          "name": "third_setting"
      },
      {
          "type": "array",
          "value": {
              "a": 1,
              "b": 2
          },
          "scope": "global",
          "name": "fourth_setting"
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
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/" with body:
    """
    {
        "name":"instance_name",
        "value":"Publisher edited"
    }
    """
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/" with body:
    """
    {
        "name":"primary_font_family",
        "value":"Oswald"
    }
    """
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/" with body:
    """
      {
          "name":"secondary_font_family",
          "value":"Lato"
      }
    """
    Then the response status code should be 200
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    And the JSON should be equal to:
    """
    [
      {
        "type":"boolean",
        "scope":"tenant",
        "value":true,
        "name":"registration_enabled"
      },
      {
        "value":"@FOSUser\/Registration\/email.txt.twig",
        "scope":"tenant",
        "type":"string",
        "name":"registration_confirmation.template"
      },
      {
        "value":{
          "contact@publisher.test":"Publisher"
        },
        "scope":"tenant",
        "type":"array",
        "name":"registration_from_email.confirmation"
      },
      {
        "value":"@FOSUser\/Resetting\/email.txt.twig",
        "scope":"tenant",
        "type":"string",
        "name":"registration_resetting.template"
      },
      {
        "value":{
          "contact@publisher.test":"Publisher"
        },
        "scope":"tenant",
        "type":"array",
        "name":"registration_from_email.resetting"
      },
      {
        "scope":"global",
        "type":"string",
        "value":"Publisher edited",
        "name":"instance_name"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"filtering_prefrences"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"user_private_preferences"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"user_favourite_articles"
      },
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
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"use_first_published_as_publish_date"
      },
      {
        "type": "boolean",
        "scope": "tenant",
        "value": false,
        "name": "override_slug_on_correction"
      },
      {
        "type": "string",
        "scope": "tenant",
        "value": "(Photo: {{ author }})",
        "name": "embedded_image_author_template"
      },
      {
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"fbia_enabled"
      },
      {
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"paywall_enabled"
      },
      {
          "type": "string",
          "scope": "tenant",
          "value": "en",
          "name": "default_language"
      },
      {
          "scope": "global",
          "value": null,
          "type": "string",
          "name": "first_setting"
      },
      {
          "scope": "global",
          "value": 123,
          "type": "string",
          "name": "second_setting"
      },
      {
          "scope": "user",
          "value": "sdfgesgts4tgse5tdg4t",
          "type": "string",
          "name": "third_setting"
      },
      {
          "type": "array",
          "value": {
              "a": 1,
              "b": 2
          },
          "scope": "global",
          "name": "fourth_setting"
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
        "value":"Lato",
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
    Then I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/settings/revert/theme"
    Then the response status code should be 204
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    And the JSON should be equal to:
    """
    [
      {
        "type":"boolean",
        "scope":"tenant",
        "value":true,
        "name":"registration_enabled"
      },
      {
        "value":"@FOSUser\/Registration\/email.txt.twig",
        "scope":"tenant",
        "type":"string",
        "name":"registration_confirmation.template"
      },
      {
        "value":{
          "contact@publisher.test":"Publisher"
        },
        "scope":"tenant",
        "type":"array",
        "name":"registration_from_email.confirmation"
      },
      {
        "value":"@FOSUser\/Resetting\/email.txt.twig",
        "scope":"tenant",
        "type":"string",
        "name":"registration_resetting.template"
      },
      {
        "value":{
          "contact@publisher.test":"Publisher"
        },
        "scope":"tenant",
        "type":"array",
        "name":"registration_from_email.resetting"
      },
      {
        "scope":"global",
        "type":"string",
        "value":"Publisher edited",
        "name":"instance_name"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"filtering_prefrences"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"user_private_preferences"
      },
      {
        "scope":"user",
        "type":"string",
        "value":"{}",
        "name":"user_favourite_articles"
      },
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
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"use_first_published_as_publish_date"
      },
      {
        "type": "boolean",
        "scope": "tenant",
        "value": false,
        "name": "override_slug_on_correction"
      },
      {
        "type": "string",
        "scope": "tenant",
        "value": "(Photo: {{ author }})",
        "name": "embedded_image_author_template"
      },
      {
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"fbia_enabled"
      },
      {
        "type":"boolean",
        "scope":"tenant",
        "value":false,
        "name":"paywall_enabled"
      },
      {
          "type": "string",
          "scope": "tenant",
          "value": "en",
          "name": "default_language"
      },
      {
          "scope": "global",
          "value": null,
          "type": "string",
          "name": "first_setting"
      },
      {
          "scope": "global",
          "value": 123,
          "type": "string",
          "name": "second_setting"
      },
      {
          "scope": "user",
          "value": "sdfgesgts4tgse5tdg4t",
          "type": "string",
          "name": "third_setting"
      },
      {
          "type": "array",
          "value": {
              "a": 1,
              "b": 2
          },
          "scope": "global",
          "name": "fourth_setting"
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
    Then the response status code should be 200
