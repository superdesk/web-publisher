@redirect-routes
@disable-fixtures
Feature: Redirecting readers from already existing routes to other existing routes
  In order to redirect readers from existing routes
  As a HTTP Client
  I want to be able to create a redirect route which will redirect from already existing route to other routes

  Background:
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |
    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

  Scenario: Creating a new 302 redirect from one to another route
    Given the following Routes:
      |  name  | type       | slug  |
      |  test  | collection | test  |
      |  sport | collection | sport |

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/redirects/" with body:
     """
      {
          "route_source": 1,
          "route_target": 2,
          "permanent": false
      }
    """
    Then the response status code should be 201

    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/redirects/" with body:
     """
      {
          "route_source": 1,
          "route_target": 2,
          "permanent": false
      }
    """
    Then the response status code should be 400

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
                "static_prefix": "/test",
                "variable_pattern":null,
                "uri":null,
                "route_name":null,
                "route_target":{
                   "requirements":{
                      "slug":"[a-zA-Z0-9*\\-_]+"
                   },
                   "id":2,
                   "content":null,
                   "static_prefix":"/sport",
                   "variable_pattern":"/{slug}",
                   "parent":null,
                   "children":[

                   ],
                   "lft":3,
                   "rgt":4,
                   "level":0,
                   "template_name":null,
		   "description":null,
		   "articles_template_name":null,
                   "type":"collection",
                   "cache_time_in_seconds":0,
                   "name":"sport",
                   "slug":"sport",
                   "position":1,
                   "articles_count":0,
                   "paywall_secured":false,
                   "_links":{
                      "self":{
                         "href":"/api/v2/content/routes/2"
                      }
                   }
                },
                "permanent":false,
                "parameters":[

                ],
                "route_source":{
                   "requirements":{
                      "slug":"[a-zA-Z0-9*\\-_]+"
                   },
                   "id":1,
                   "content":null,
                   "static_prefix":"/test",
                   "variable_pattern":"/{slug}",
                   "parent":null,
                   "children":[

                   ],
                   "lft":1,
                   "rgt":2,
                   "level":0,
		   "template_name":null,
		   "description": null,
                   "articles_template_name":null,
                   "type":"collection",
                   "cache_time_in_seconds":0,
                   "name":"test",
                   "slug":"test",
                   "position":0,
                   "articles_count":0,
                   "paywall_secured":false,
                   "_links":{
                      "self":{
                         "href":"/api/v2/content/routes/1"
                      }
                   }
                }
             }
          ]
       }
    }
    """
    Then the response status code should be 200

    When I send a GET request to "/test"
    Then the response status code should be 302
    And the header "location" should be equal to "http://localhost/sport"
