Feature: Opening pages with redirection functions used in template
  In order to be redirected to other page
  As a HTTP Client
  I want to be able to open url

  Scenario: Redirecting request to homepage
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/routes/" with body:
     """
      {
          "name": "301 Redirect",
          "slug": "redirect_301",
          "type": "content",
          "templateName": "route_with_301_redirect.html.twig"
      }
    """
    Then the response status code should be 201
    When I go to "/redirect_301"
    Then the response status code should be 200
    And I should see "Homepage theme_test"

  Scenario: Redirecting request to 404 error page
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/routes/" with body:
     """
      {
          "name": "404 Redirect",
          "slug": "redirect_404",
          "type": "content",
          "templateName": "404_redirect.html.twig"
      }
    """
    Then the response status code should be 201
    When I go to "/redirect_404"
    Then the response status code should be 404
    And I should see "Requested page was not found"


