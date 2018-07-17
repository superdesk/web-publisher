@user_commands
Feature: Checking if create user command working properly
  In order to create user
  As a console command
  I want to be able to check if user is created properly

  Scenario: Creating new user
    When I run "fos:user:create --tenant=123abc newTestAccount null@sourcefabric.org superSecretPassword" command
    Then I should see "Created user newTestAccount" in the output

  Scenario: Creating duplicated user
    When I run "fos:user:create --tenant=123abc newTestAccount null@sourcefabric.org superSecretPassword" command
    Then I should see "User with username newTestAccount already exists!" in the output

  Scenario: Creating user for wrong tenant
    When I run "fos:user:create --tenant=badcode newTestAccount null@sourcefabric.org superSecretPassword" command
    Then I should see "Tenant with code badcode was not found" in the exception
