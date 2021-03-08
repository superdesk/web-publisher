@user_commands
@disable-fixtures
Feature: Checking if create user command working properly
  In order to create user
  As a console command
  I want to be able to check if user is created properly

  Scenario: Creating new user
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |
    When I run the "swp:user:create" command with options:
      | username | newTestAccount        |
      | email    | null@sourcefabric.org |
      | password | superSecretPassword   |
      | --tenant | 123abc                |
    Then the command output should be "Created user newTestAccount"

  Scenario: Creating duplicated user
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |
    When I run the "swp:user:create" command with options:
      | username | newTestAccount        |
      | email    | null@sourcefabric.org |
      | password | superSecretPassword   |
      | --tenant | 123abc                |
    Then the command output should be "User with username newTestAccount already exists!"

  Scenario: Creating user for wrong tenant
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |
    When I run the "swp:user:create" command with options:
      | username | newTestAccount        |
      | email    | null@sourcefabric.org |
      | password | superSecretPassword   |
      | --tenant | badcode               |
    Then the command exception should be "RuntimeException"
    And the command exception message should be "Tenant with code badcode was not found"
