@user
Feature: Registering and login as new user publisher
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
    Then print last response
    Then the response status code should be 200
    And the response should contain "The user has been created successfully"
    And the response should contain "An email has been sent to null@sourcefabric.org. It contains an activation link you must click to activate your account."

    When I grab the confirmation url and follow it
    Then the response status code should be 302
    And I follow the redirection
    Then the response status code should be 200
    Then print last response
    And the response should contain "Logged in as null_user"
    And the response should contain "Congrats null_user, your account is now activated."


    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/auth/" with body:
    """
    {
      "username": "null_user",
      "password": "superSecret"
    }
    """
    Then the response status code should be 200
    And the JSON node "user.username" should be equal to "null_user"

    When I add "Content-Type" header equal to "application/json"
    Given I am authenticated as "null_user"
    And I send a "GET" request to "/api/v2/content/lists/"
    Then the response status code should be 403
