@authors
@disable-fixtures
Feature: Delete Authors
  In order to get rid off unwanted authors
  As a HTTP Client
  I want to be able to delete them via API

  Background:
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |
    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |
    Given the following Articles:
      | title               | route      | status    | isPublishable  | publishedAt | authors       |
      | First Test Article  | Sports     | published | true           | 2020-01-20  | Adam          |
      | Second Test Article | Politics   | published | true           | 2020-01-20  | Rafal,Tomek   |
      | Third Test Article  | Sports     | published | true           | 2020-01-20  | Adam          |

  Scenario: Delete exiting author
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "DELETE" request to "/api/v2/authors/1"
    Then the response status code should be 204
