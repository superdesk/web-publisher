Feature: Listing single route by slug and parent
  In case there are routes with the same slugs but under different parents
  As a HTTP Client
  I want to be able to render it by providing slug and parent to display the proper route

  Scenario: Listing single route's url by slug and parent
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Politics",
          "type": "collection",
          "templateName": "route_by_slug_and_parent.html.twig"
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Politics"
    And the JSON node slug should be equal to "politics"
    And the JSON node staticPrefix should be equal to "/politics"
    And the JSON node id should be equal to "7"
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Test route",
          "type": "content",
          "parent": 7
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Test route"
    And the JSON node parent should be equal to "7"
    And the JSON node slug should be equal to "test-route"
    And the JSON node staticPrefix should be equal to "/politics/test-route"
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/content/routes/" with body:
     """
      {
          "name": "Test route 2",
          "type": "content",
          "slug": "test-route"
      }
    """
    Then the response status code should be 201
    And the JSON node name should be equal to "Test route 2"
    And the JSON node slug should be equal to "test-route"
    And the JSON node staticPrefix should be equal to "/test-route"
    When I go to "/politics"
    Then the response status code should be 200
    And I should see "http://localhost/politics/test-route"
