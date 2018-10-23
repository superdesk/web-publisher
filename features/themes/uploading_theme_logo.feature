@articles
Feature: Validate custom fields
  In order to handle custom fields
  As a HTTP Client
  I want to be able to check if the custom fields were processed properly

  Scenario: Submitting and publishing a package with extra custom fields
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/theme/logo_upload/" with parameters:
      | key     | value      |
      | logo    | @logo.png  |
    Then the response status code should be 201
