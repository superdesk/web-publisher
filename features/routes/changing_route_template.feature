@routes
Feature: Changing route template
  In order to change the visual aspect of the website
  As a HTTP Client
  I want to be able to change route template

  Scenario: Change route template
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route": {
          "name": "Politics",
          "slug": "politics",
          "type": "collection"
        }
      }
    """
    Then the response status code should be 201
    And the JSON node "paywallSecured" should be false
    And the JSON node "id" should be equal to "7"

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v1/content/routes/7" with body:
     """
      {
        "route":{
          "templateName": "masterCategory.html.twig"
        }
      }
     """
    Then the response status code should be 200
    And the JSON node "templateName" should be equal to "masterCategory.html.twig"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v1/content/routes/7"
    Then the response status code should be 200
    And the JSON node "templateName" should be equal to "masterCategory.html.twig"
