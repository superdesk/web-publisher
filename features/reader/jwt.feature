@reader
Feature: Registering and login as new publisher reader
  In order to work with readers accounts
  As a HTTP Client
  I want to be able to register new account and use it for login

  Scenario: Registering new user
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/users/register/" with body:
    """
    {
        "email": "null@sourcefabric.org",
        "username": "null_user",
        "plainPassword": {
          "first": "superSecret",
          "second": "superSecret"
        }
    }
    """
    Then the response status code should be 200
    And the JSON node 'url' should exist

    When I send a "GET" request to "/register/confirm/abcdefghijklmn"
    Then the response status code should be 302
    And I follow the redirection
    Then the response status code should be 200
    And the response should contain "Logged in as null_user"
    And the response should contain "Congrats null_user, your account is now activated."

    When I send a "POST" request to "/security/login_check" with parameters:
      | key       | value       |
      | _username | null_user   |
      | _password | superSecret |

    Then the response status code should be 200
    Then the JSON node "token" should exist
    And we save it into "token"
    Then the JSON node "refresh_token" should exist
    And we save it into "refresh"

    When I add "Authorization" header equal to "Bearer <<token>>"
    And I send a "GET" request to "/api/v2/users/profile/4"
    Then the response status code should be 200
    And the JSON node "id" should be equal to 4

    When I send a "POST" request to "/api/v2/token/refresh?refresh_token=<<refresh>>"

    Then the response status code should be 200
    Then the JSON node "token" should exist
    And we save it into "token"
    And I add "Authorization" header equal to "Bearer <<token>>"
    And I send a "GET" request to "/api/v2/users/profile/4"
    Then the response status code should be 200
    And the JSON node "id" should be equal to 4