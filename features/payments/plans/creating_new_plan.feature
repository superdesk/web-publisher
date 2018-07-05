@payments @plans @api
Feature: Adding a plan
  In order to purchase subscriptions by the clients
  As a HTTP Client
  I want to create a new plan

  Scenario: Add a new plan
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/plans/" with body:
     """
      {
        "plan": {
          "name": "Professional Plan",
          "code": "pro-plan",
          "amount": 5000,
          "interval": "month",
          "intervalCount": 1,
          "currency": "USD"
        }
      }
    """
    Then the response status code should be 201
    And the JSON nodes should contain:
      | id               | 1                 |
      | name             | Professional Plan |
      | code             | pro-plan          |
      | amount           | 5000              |
      | interval         | month             |
      | interval_count   | 1                 |
      | currency         | USD               |
    #And the JSON node "active" should be true
