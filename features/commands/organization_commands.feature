@organization_commands
@disable-fixtures
Feature: Checking if create and update organization commands working properly
  In order to create and update organization
  As a console command
  I want to be able to check if organization is created and updated properly

  Scenario: Creating new organization with secret token set
    When I run the "swp:organization:create" command with options:
      | name          | TestOrganization |
      | --env         | test             |
      | --secretToken | secret_token     |
    Then the command output should be "Organization TestOrganization (code:"
    And the command output should be ", secret token: secret_token) has been created and enabled!"

  Scenario: Creating new organization
    When I run the "swp:organization:create" command with options:
      | name          | TestOrganization1 |
      | --env         | test              |
    Then the command output should be "Organization TestOrganization1 (code: "
    And the command output should be "has been created and enabled!"

  Scenario: Creating new organization
    When I run the "swp:organization:create" command with options:
      | name          | TestOrganization2 |
      | --env         | test              |
      | --disabled    | true              |
    Then the command output should be "Organization TestOrganization2 (code: "
    And the command output should be "has been created and disabled!"


  Scenario: Updating organization secret token
    When I run the "swp:organization:update" command with options:
      | name          | TestOrganization |
      | --env         | test             |
      | --secretToken | new_secret_token |
    Then the command output should be "Organization TestOrganization (code: "
    And the command output should be ", secret token: new_secret_token) has been updated and is enabled"

  Scenario: Disabling organization
    When I run the "swp:organization:update" command with options:
      | name          | TestOrganization |
      | --env         | test             |
      | --disabled    | true             |
    Then the command output should be "Organization TestOrganization (code: "
    And the command output should be ", secret token: new_secret_token) has been updated and is disabled!"

  Scenario: Create default organization
    When I run the "swp:organization:create" command with options:
      | --env         | test             |
      | --default     |                  |
    Then the command output should be "Organization default (code: 123456)"
