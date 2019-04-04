@organization_commands
@disable-fixtures
Feature: Reprocessing already send to publisher packages
  In order to apply new rules or content modifications to already pushed to publisher packages
  As a console command
  I want to be able to pick and reprocess packages

  Scenario: Reprocessing packages with commands:
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following organization publishing rule:
    """
      {
        "name":"Test rule",
        "description":"Test rule description",
        "priority":1,
        "expression":"true == true",
        "configuration":[
          {
            "key":"destinations",
            "value":[
              {
                "tenant":"123abc"
              }
            ]
          }
        ]
      }
    """

    Given the following Package ninjs:
    """
      {
          "language":"en","headline":"Test Package","version":"2","guid":"16e111d5","priority":6,"type":"text",
          "authors":[{"name":"Tom Doe","role":"editor"}]
      }
    """

    Given default tenant with code "123abc"
    Given the following Package ninjs:
    """
      {
          "language":"en","headline":"Author Package","version":"2","guid":"fc0a805e","priority":6,"type":"text",
          "authors":[{"name":"John Doe","role":"editor"}]
      }
    """

    When I run the "swp:package:process" command with options:
      | --dry-run    | true   |
      | --tenant     | 123abc |
    Then the command output should be "Packages found: 2"
    And the command output should be "Processing package with guid: 16e111d5"
    And the command output should be "Processing package with guid: fc0a805e"

    When I run the "swp:package:process" command with options:
      | --dry-run    | true     |
      | --tenant     | 123abc   |
      | --authors    | John Doe |
    Then the command output should be "Packages found: 1"
    And the command output should be "Processing package with guid: fc0a805e"

    When I run the "swp:package:process" command with options:
      | --dry-run    | true   |
      | --tenant     | 123abc |
      | --statuses   | new    |
    Then the command output should be "Packages found: 2"
    And the command output should be "Processing package with guid: 16e111d5"
    And the command output should be "Processing package with guid: fc0a805e"

    When I run the "swp:package:process" command with options:
      | --dry-run    | true      |
      | --tenant     | 123abc    |
      | --statuses   | published |
    Then the command output should be "Packages found: 0"

    Given default tenant with code "123abc"
    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

    Given the following tenant publishing rule:
    """
      {
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"article.getPackage().getLanguage() == 'en'",
          "configuration":[
            {
              "key":"route",
              "value":1
            },
            {
              "key":"published",
              "value":true
            }
          ]
       }
    """

    When I run the "swp:package:process" command with options:
      | --tenant     | 123abc    |
      | --statuses   | new |
    Then the command output should be "Packages found: 2"
    And the command output should be "Processing package with guid: 16e111d5"
    And the command output should be "Processing package with guid: fc0a805e"


    When I run the "swp:package:process" command with options:
      | --dry-run    | true      |
      | --tenant     | 123abc    |
      | --statuses   | new |
    Then the command output should be "Packages found: 0"
