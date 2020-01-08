@analytics
@disable-fixtures
Feature: Export articles analytics report
  In order to use data about articles externally
  As a HTTP Client
  I want to be able to export existing analytics data to CSV file

  Background:
    Given the current date time is "2019-03-10 09:00"
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      | code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc |

    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |


  Scenario: Export analytics data from last 30 days to CSV
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics"
    Then the response status code should be 201

  Scenario: Listing analytics reports
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/export/analytics"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    {
       "page":1,
       "limit":10,
       "pages":1,
       "total":1,
       "_links":{
          "self":{
             "href":"/api/v2/export/analytics?page=1&limit=10"
          },
          "first":{
             "href":"/api/v2/export/analytics?page=1&limit=10"
          },
          "last":{
             "href":"/api/v2/export/analytics?page=1&limit=10"
          }
       },
       "_embedded":{
          "_items":[
             {
                "id":1,
                "asset_id":"analytics-2019-03-10-09:00:00.csv",
                "user":{
                   "id":1,
                   "username":"test.user",
                   "email":"test.user@sourcefabric.org",
                   "last_login":null,
                   "roles":[
                      "ROLE_INTERNAL_API"
                   ],
                   "first_name":null,
                   "last_name":null,
                   "about":null,
                   "created_at":"2019-03-10T09:00:00+0000",
                   "updated_at":null
                },
                "status":"processing",
                "created_at":"2019-03-10T09:00:00+00:00",
                "updated_at":null
             }
          ]
       }
    }
    """
