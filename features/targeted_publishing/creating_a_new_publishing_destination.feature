@publish_destination
Feature: Creating a new publishing destination

  Scenario: Creating a new targeted publishing destinations
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/{version}/organization/destinations/" with body:
     """
      {
        "publish_destination":{
          "tenant":"123abc",
          "route":5,
          "fbia":false,
          "published":false,
          "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
        }
      }
    """
    Then the response status code should be 200
    And the JSON nodes should contain:
      | organization.id         | 1      |
      | tenant.code             | 123abc |
      | route.id                | 5      |
    And the JSON node "published" should be false
    And the JSON node "fbia" should be false
