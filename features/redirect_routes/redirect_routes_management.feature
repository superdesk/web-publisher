@redirect-routes
@disable-fixtures
Feature: Manage Redirect Routes
  In order manage redirect routes
  As a HTTP Client
  I want to be able to create/update/edit them via API

  Background:
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |
    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |
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

  Scenario: Delete exiting redirect route
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "DELETE" request to "/api/v2/redirects/1"
    Then the response status code should be 204

  Scenario: Update exiting redirect route
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/redirects/1" with body:
     """
      {
          "uri": "https://example.com",
          "permanent": true
      }
    """
    Then the response status code should be 200

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
                "uri":"https://example.com",
                "route_name":"/business",
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
