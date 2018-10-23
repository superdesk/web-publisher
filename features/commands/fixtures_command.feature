@organization_commands
Feature: Checking if create and update organization commands working properly
  In order to create and update organization
  As a console command
  I want to be able to check if organization is created and updated properly

  Scenario: Load fixtures
    Given I run "doctrine:fixtures:load --no-interaction" command
    Then I should see "Organization TestOrganization (code: " in the output
