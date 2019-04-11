@publish_destination
Feature: Update existing publish destination

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
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PATCH" request to "/api/v2/organization/destinations/1" with body:
     """
      {
          "tenant":"123abc",
          "route":6,
          "is_published_fbia":false,
          "published":true,
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