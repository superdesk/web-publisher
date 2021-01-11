@redirect-routes
@disable-fixtures
Feature: Redirecting to an external URL
  In order to redirect readers to the external URL
  As a HTTP Client
  I want to be able to define the redirect route

  Background:
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |
    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

  Scenario: Creating a new 302 redirect route to redirect to an external URL
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/redirects/" with body:
     """
      {
          "route_name": "/business",
          "uri": "https://google.com",
          "permanent": false
      }
    """
    Then the response status code should be 201

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a GET request to "/api/v2/redirects/"
    Then the JSON should be equal to:
    """
    {
       "page":1,
       "limit":10,
       "pages":1,
       "total":1,
       "_links":{
          "self":{
             "href":"/api/v2/redirects/?page=1&limit=10"
          },
          "first":{
             "href":"/api/v2/redirects/?page=1&limit=10"
          },
          "last":{
             "href":"/api/v2/redirects/?page=1&limit=10"
          }
       },
       "_embedded":{
          "_items":[
             {
                "requirements":[

                ],
                "id":1,
                "content":null,
                "static_prefix":"/business",
                "variable_pattern":null,
                "uri":"https://google.com",
                "route_name":"/business",
                "route_target":null,
                "permanent":false,
                "parameters":[

                ],
                "route_source": null
             }
          ]
       }
    }
    """
    Then the response status code should be 200

    When I send a GET request to "/business"
    Then the response status code should be 302
    And the header "location" should be equal to "https://google.com"

    When I go to "/not-existing-route"
    Then the response status code should be 404

  Scenario: Creating a new 301 redirect route to redirect to an external URL
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/redirects/" with body:
     """
      {
          "route_name": "/sports",
          "uri": "https://google.com",
          "permanent": true
      }
    """
    Then the response status code should be 201

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a GET request to "/api/v2/redirects/"
    Then the JSON should be equal to:
    """
    {
       "page":1,
       "limit":10,
       "pages":1,
       "total":2,
       "_links":{
          "self":{
             "href":"/api/v2/redirects/?page=1&limit=10"
          },
          "first":{
             "href":"/api/v2/redirects/?page=1&limit=10"
          },
          "last":{
             "href":"/api/v2/redirects/?page=1&limit=10"
          }
       },
       "_embedded":{
          "_items":[
             {
                "requirements":[

                ],
                "id":1,
                "content":null,
                "static_prefix":"/business",
                "variable_pattern":null,
                "uri":"https://google.com",
                "route_name":"/business",
                "route_target":null,
                "permanent":false,
                "parameters":[

                ],
                "route_source": null
             },
             {
                "requirements":[

                ],
                "id":2,
                "content":null,
                "static_prefix":"/sports",
                "variable_pattern":null,
                "uri":"https://google.com",
                "route_name":"/sports",
                "route_target":null,
                "permanent":true,
                "parameters":[

                ],
                "route_source": null
             }
          ]
       }
    }
    """
    Then the response status code should be 200

    When I send a GET request to "/sports"
    Then the response status code should be 301
    And the header "location" should be equal to "https://google.com"

  Scenario: Creating a new 302 redirect route with a param
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/redirects/" with body:
     """
      {
          "route_name": "/business?param=1",
          "uri": "https://google.com",
          "permanent": false
      }
    """
    Then the response status code should be 201

    When I send a GET request to "/business?param=1"
    Then the response status code should be 302
    And the header "location" should be equal to "https://google.com"

    When I send a GET request to "/business?fake=1"
    Then the response status code should be 302
    And the header "location" should be equal to "https://google.com"

    When I send a GET request to "/business"
    Then the response status code should be 302
    And the header "location" should be equal to "https://google.com"