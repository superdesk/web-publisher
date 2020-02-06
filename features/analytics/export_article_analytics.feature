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

    Given the following Articles:
      | title               | route      | status    | isPublishable  | publishedAt | authors |
      | First Test Article  | Sports     | published | true           | 2020-01-20  | Adam    |
      | Second Test Article | Politics   | published | true           | 2020-01-20  | Rafal   |
      | Third Test Article  | Sports     | published | true           | 2020-01-20  | Adam    |
    And I wait 2 seconds

  Scenario: Export analytics data by different filters
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics/?start=2020-01-20&end=2020-01-23&route[]=1"
    Then the response status code should be 201

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/export/analytics/"
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
             "href":"/api/v2/export/analytics/?page=1&limit=10"
          },
          "first":{
             "href":"/api/v2/export/analytics/?page=1&limit=10"
          },
          "last":{
             "href":"/api/v2/export/analytics/?page=1&limit=10"
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
                "status":"completed",
                "filters": {
                    "term": "",
                    "start": "2020-01-20T00:00:00+00:00",
                    "end": "2020-01-23T00:00:00+00:00",
                    "routes": [
                        "Sports"
                    ],
                    "authors": []
                },
                "created_at":"2019-03-10T09:00:00+00:00",
                "updated_at":"2019-03-10T09:00:00+00:00",
                "_links": {
                    "download": {
                        "href": "http://localhost/analytics/export/analytics-2019-03-10-09:00:00.csv"
                    }
                }
             }
          ]
       }
    }
    """
    And the CSV file "/public/uploads/swp/123456/exports/analytics-2019-03-10-09:00:00.csv" should contain 4 rows

    Given the current date time is "2019-03-10 10:00"
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics/?start=2020-01-20&end=2020-01-23&route[]=2"
    Then the response status code should be 201
    And the CSV file "/public/uploads/swp/123456/exports/analytics-2019-03-10-10:00:00.csv" should contain 3 rows

    Given the current date time is "2019-03-10 11:00"
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics/?start=2020-01-20&end=2020-01-23&author[]=Tom"
    Then the response status code should be 201
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/export/analytics/"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
     {
       "page":1,
       "limit":10,
       "pages":1,
       "total":3,
       "_links":{
          "self":{
             "href":"/api/v2/export/analytics/?page=1&limit=10"
          },
          "first":{
             "href":"/api/v2/export/analytics/?page=1&limit=10"
          },
          "last":{
             "href":"/api/v2/export/analytics/?page=1&limit=10"
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
                "status":"completed",
                "filters":{
                   "term":"",
                   "start":"2020-01-20T00:00:00+00:00",
                   "end":"2020-01-23T00:00:00+00:00",
                   "routes":[
                      "Sports"
                   ],
                   "authors":[

                   ]
                },
                "created_at":"2019-03-10T09:00:00+00:00",
                "updated_at":"2019-03-10T09:00:00+00:00",
                "_links":{
                   "download":{
                      "href":"http://localhost/analytics/export/analytics-2019-03-10-09:00:00.csv"
                   }
                }
             },
             {
                "id":2,
                "asset_id":"analytics-2019-03-10-10:00:00.csv",
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
                "status":"completed",
                "filters":{
                   "term":"",
                   "start":"2020-01-20T00:00:00+00:00",
                   "end":"2020-01-23T00:00:00+00:00",
                   "routes":[
                      "Politics"
                   ],
                   "authors":[

                   ]
                },
                "created_at":"2019-03-10T10:00:00+00:00",
                "updated_at":"2019-03-10T10:00:00+00:00",
                "_links":{
                   "download":{
                      "href":"http://localhost/analytics/export/analytics-2019-03-10-10:00:00.csv"
                   }
                }
             },
             {
                "id":3,
                "asset_id":"analytics-2019-03-10-11:00:00.csv",
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
                "status":"completed",
                "filters":{
                   "term":"",
                   "start":"2020-01-20T00:00:00+00:00",
                   "end":"2020-01-23T00:00:00+00:00",
                   "routes":[

                   ],
                   "authors":[
                      "Tom"
                   ]
                },
                "created_at":"2019-03-10T11:00:00+00:00",
                "updated_at":"2019-03-10T11:00:00+00:00",
                "_links":{
                   "download":{
                      "href":"http://localhost/analytics/export/analytics-2019-03-10-11:00:00.csv"
                   }
                }
             }
          ]
       }
    }
    """
    And the CSV file "/public/uploads/swp/123456/exports/analytics-2019-03-10-11:00:00.csv" should contain 2 rows

    Given the current date time is "2019-03-10 12:00"
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics/?start=2020-01-20&end=2020-01-23&author[]=Rafal"
    Then the response status code should be 201
    And the CSV file "/public/uploads/swp/123456/exports/analytics-2019-03-10-12:00:00.csv" should contain 3 rows

    Given the current date time is "2019-03-10 13:00"
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics/?start=2020-01-20&end=2020-01-23&author[]=Adam"
    Then the response status code should be 201
    And the CSV file "/public/uploads/swp/123456/exports/analytics-2019-03-10-13:00:00.csv" should contain 4 rows

    Given the current date time is "2019-03-10 14:00"
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics/?start=2020-01-20&end=2020-01-23&term=second"
    Then the response status code should be 201
    And the CSV file "/public/uploads/swp/123456/exports/analytics-2019-03-10-14:00:00.csv" should contain 3 rows

    Given the current date time is "2019-03-10 15:00"
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/export/analytics/?start=2020-01-20&end=2020-01-23&term=fake"
    Then the response status code should be 201
    And the CSV file "/public/uploads/swp/123456/exports/analytics-2019-03-10-15:00:00.csv" should contain 2 rows
