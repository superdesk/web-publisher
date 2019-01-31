@settings
Feature: Settings bulk update

  Scenario: Update multiple settings
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/{version}/settings/bulk/" with body:
    """
    {
      "settings":{
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
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
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
    And I send a "PATCH" request to "/api/{version}/settings/bulk/" with body:
    """
    {
      "settings":{
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
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
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
    And I send a "POST" request to "/api/{version}/theme/logo_upload/" with parameters:
      | key     | value      |
      | logo    | @logo.png  |
    Then the response status code should be 201
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/{version}/settings/bulk/" with body:
    """
    {
      "settings":{
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
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | [14].name               | primary_font_family                 |
      | [14].value              | Lato                                |
      | [15].name               | secondary_font_family               |
      | [15].value              | Oswald                              |
      | [9].name                | theme_logo                          |
      | [9].value               | .png                                |
      | [16].name               | body_font_size                      |
      | [16].value              | 16                                  |
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/{version}/settings/bulk/" with body:
    """
    {
      "settings":{
        "bulk":[
          {
            "name":"switch",
            "value":false
          }
        ]
      }
    }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/{version}/settings/"
    Then the response status code should be 200
    And the JSON nodes should contain:
      | [17].name                | switch   |
    And the JSON node "[17].value" should be false
