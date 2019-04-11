@routes
Feature: Changing route template
  In order to change the visual aspect of the website
  As a HTTP Client
  I want to be able to change route template

  Scenario: Change route template
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/routes/" with body:
     """
      {
          "name": "Politics",
          "slug": "politics",
          "type": "collection"
      }ContentListType
    """
    Then the response status code should be 201
    And the JSON node "paywall_secured" should be false
    And the JSON node "id" should be equal to "7"

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/routes/7" with body:
     """
      {
          "templateName": "masterCategory.html.twig"
      }
     """
    Then the response status code should be 200
    And the JSON node "template_name" should be equal to "masterCategory.html.twig"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/routes/7"
    Then the response status code should be 200
    And the JSON node "template_name" should be equal to "masterCategory.html.twig"
