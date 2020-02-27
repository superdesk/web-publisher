@tenant_commands
@disable-fixtures
Feature: Checking if create and update tenant commands working properly
  In order to create and update tenant
  As a console command
  I want to be able to check if tenant is created and updated properly

  Scenario: Creating new tenant for default organization
    When I run the "swp:organization:create" command with options:
      | --env         | test             |
      | --default     | ""               |
    Then the command output should be "Organization default (code: 123456)"

    When I run the "swp:tenant:create" command with options:
      | --env         | test             |
      | --default     | ""               |
    Then the command output should be "Tenant Default tenant (code: 123abc) has been created and enabled!"
