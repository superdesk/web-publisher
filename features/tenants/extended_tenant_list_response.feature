@disable-fixtures
Feature: getting tenants lists with their routes and content lists
  In order to limit ui calls for tenants listing
  As a HTTP Client
  I want to get tenants list with their route and/or content lists

  Scenario: Getting tenants list
    Given the following Tenants:
      | organization | name   | subdomain | domain_name | enabled | default | code   |
      | Default      | first  |           | localhost   | true    | true    | 123abc |
      | Default      | second |           | example.com | true    | true    | 456def |

    Given default tenant with code "123abc"
    Given the following Routes:
      |  name  | type       | slug        |
      |  test1 | collection | test        |
      |  test2 | content    | testcontent |

    Given the following Content Lists:
      | name                | type   |
      | test content list 1 | manual |

    Given default tenant with code "456def"
    Given the following Routes:
      |  name  | type       | slug        |
      |  test3 | collection | test        |
      |  test4 | content    | testcontent |

    Given the following Content Lists:
      | name                | type   |
      | test content list 2 | manual |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given I am authenticated as "test.user"
    When I send a "GET" request to "api/v2/tenants/?withRoutes=true"
    Then the response status code should be 200
    And the JSON node '_embedded._items[0].routes' should exist
    And the JSON node '_embedded._items[0].routes[0].name' should be equal to "test1"
    And the JSON node '_embedded._items[0].routes[1].name' should be equal to "test2"
    And the JSON node '_embedded._items[1].routes' should exist
    And the JSON node '_embedded._items[1].routes[0].name' should be equal to "test3"
    And the JSON node '_embedded._items[1].routes[1].name' should be equal to "test4"

    Given I am authenticated as "test.user"
    When I send a "GET" request to "api/v2/tenants/?withContentLists=true"
    Then the response status code should be 200
    And the JSON node '_embedded._items[0].content_lists' should exist
    And the JSON node '_embedded._items[0].content_lists[0].name' should be equal to "test content list 1"
    And the JSON node '_embedded._items[1].content_lists' should exist
    And the JSON node '_embedded._items[1].content_lists[0].name' should be equal to "test content list 2"

    Given I am authenticated as "test.user"
    When I send a "GET" request to "api/v2/tenants/?withContentLists=true&withRoutes=true"
    Then the response status code should be 200
    And the JSON node '_embedded._items[0].routes' should exist
    And the JSON node '_embedded._items[1].routes' should exist
    And the JSON node '_embedded._items[0].content_lists' should exist
    And the JSON node '_embedded._items[1].content_lists' should exist
