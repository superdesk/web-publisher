@organization_commands
Feature: Checking if create and update organization commands working properly
  In order to create and update organization
  As a console command
  I want to be able to check if organization is created and updated properly

  Scenario: Creating new organization with secret token set
    When I run "swp:organization:create TestOrganization --env=test --secretToken secret_token" command
    Then I should see "Organization TestOrganization (code: " in the output
    And I should see ", secret token: secret_token) has been created and enabled!" in the output

  Scenario: Creating new organization
    When I run "swp:organization:create TestOrganization1 --env=test" command
    Then I should see "Organization TestOrganization1 (code: " in the output
    And I should see "has been created and enabled!" in the output

  Scenario: Creating new organization
    When I run "swp:organization:create TestOrganization2 --env=test --disabled" command
    Then I should see "Organization TestOrganization2 (code: " in the output
    And I should see "has been created and disabled!" in the output


  Scenario: Updating organization secret token
    When I run "swp:organization:update TestOrganization --env=test --secretToken new_secret_token" command
    Then I should see "Organization TestOrganization (code: " in the output
    And I should see ", secret token: new_secret_token) has been updated and is enabled!" in the output

  Scenario: Disabling organization
    When I run "swp:organization:update TestOrganization --env=test --disabled" command
    Then I should see "Organization TestOrganization (code: " in the output
    And I should see ", secret token: new_secret_token) has been updated and is disabled!" in the output