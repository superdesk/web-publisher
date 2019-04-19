@publish_destination
Feature: Override existing publish destination by tenant

  Scenario: Creating a new targeted publishing destinations
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"123abc",
          "route":5,
          "is_published_fbia":false,
          "published":false,
          "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
      }
    """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenant.code             | 123abc |
      | route.id                | 5      |
    And the JSON node "published" should be false
    And the JSON node "is_published_fbia" should be false
    And the JSON node "paywall_secured" should be false
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"123abc",
          "route":6,
          "is_published_fbia":false,
          "published":true,
          "paywall_secured":true,
          "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
      }
    """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenant.code             | 123abc |
      | route.id                | 6      |
    And the JSON node "published" should be true
    And the JSON node "is_published_fbia" should be false
    And the JSON node "paywall_secured" should be true
    And I am authenticated as "test.client2"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "http://client2.localhost/api/v2/content/routes/" with body:
     """
      {
          "name": "My route",
          "type": "collection"
      }
    """
    Then the response status code should be 201
    And the JSON node "id" should be equal to "7"
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "http://client2.localhost/api/v2/organization/destinations/" with body:
     """
      {
          "tenant":"678iop",
          "route":7,
          "is_published_fbia":false,
          "published":true,
          "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
      }
    """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenant.code             | 678iop |
      | route.id                | 7      |
    And the JSON node "published" should be true
    And the JSON node "is_published_fbia" should be false
    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/organization/rules/evaluate" with body:
     """
     {"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}
     """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenants[0].tenant.code  | 123abc |
      | tenants[1].tenant.code  | 678iop |
      | tenants[0].route.id     | 6      |
      | tenants[1].route.id     | 7      |
    And the JSON node "tenants[0].paywall_secured" should be true
    And the JSON node "tenants[1].paywall_secured" should be false
