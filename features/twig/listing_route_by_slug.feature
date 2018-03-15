Feature: Listing single route by slug or name
  In order to list route by slug or name
  As a HTTP Client
  I want to be able to render its url

  Scenario: Listing single route's url by slug
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route": {
          "name": "Authors",
          "slug": "authors",
          "type": "collection",
          "templateName": "route_by_slug.html.twig"
        }
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Authors"
    And the JSON node slug should be equal to "authors"
    And the JSON node staticPrefix should be equal to "/authors"
    And the JSON node id should be equal to "7"
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
        "route": {
          "name": "Test route",
          "type": "content",
          "parent": 7
        }
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Test route"
    And the JSON node slug should be equal to "test-route"
    And the JSON node staticPrefix should be equal to "/authors/test-route"
    When I go to "/authors"
    Then the response status code should be 200
    And I should see "http://localhost/authors/test-route"
