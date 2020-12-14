@settings
Feature: Settings bulk update

  Scenario: Update multiple settings
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/bulk/" with body:
    """
    {
      "bulk":[
        {
          "name":"primary_font_family",
          "value":"Lato"
        },
        {
          "name":"secondary_font_family",
          "value":"Oswald",
          "scope":"scope"
        }
      ]
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    Then the response status code should be 200
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
        "value":"@SWPUser\/Registration\/email.txt.twig",
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
        "value":"@SWPUser\/Resetting\/email.txt.twig",
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
          "value": "",
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
        "name":"primary_font_family"
      },
      {
        "label":"Secondary Font Family",
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

  Scenario: Allow to change only name and value of setting
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/bulk/" with body:
    """
    {
      "bulk":[
        {
          "name":"primary_font_family",
          "value":"Lato"
        },
        {
          "name":"secondary_font_family",
          "value":"Oswald",
          "scope":"fake"
        }
      ]
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    Then the response status code should be 200
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
        "value":"@SWPUser\/Registration\/email.txt.twig",
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
        "value":"@SWPUser\/Resetting\/email.txt.twig",
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
          "value": "",
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
        "name":"primary_font_family"
      },
      {
        "label":"Secondary Font Family",
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

  Scenario: Checking if theme_logo setting is not overridden
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/theme/logo_upload/" with parameters:
      | key     | value      |
      | logo    | @logo.png  |
    Then the response status code should be 201
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/bulk/" with body:
    """
    {
      "bulk":[
        {
          "name":"primary_font_family",
          "value":"Lato"
        },
        {
          "name":"secondary_font_family",
          "value":"Oswald"
        },
        {
          "name":"switch",
          "value":true
        },
        {
          "name":"body_font_size",
          "value":16
        }
      ]
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | [22].name               | primary_font_family                 |
      | [22].value              | Lato                                |
      | [23].name               | secondary_font_family               |
      | [23].value              | Oswald                              |
      | [9].name               | theme_logo                          |
      | [9].value              | .png                                |
      | [24].name               | body_font_size                      |
      | [24].value              | 16                                  |
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/bulk/" with body:
    """
    {
      "bulk":[
        {
          "name":"switch",
          "value":false
        }
      ]
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | [25].name                | switch   |
    And the JSON node "[25].value" should be false

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/settings/bulk/" with body:
    """
    {
      "bulk":[
        {
          "name":"primary_font_family",
          "value":""
        }
      ]
    }
    """
    Then the response status code should be 200

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/settings/"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | [22].name               | primary_font_family   |
    And the JSON node "[18].value" should be equal to ""
