@targeted_publishing
Feature: Managing targeted publishing destinations

  Scenario: Creating a new targeted publishing destinations
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PUT" request to "/api/{version}/organization/destinations/" with body:
     """
      {
        "publish_destination":{
          "publishDestinations":[
            {
              "tenant":"123abc",
              "route":5,
              "fbia":false,
              "published":false,
              "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
            }
          ]
        }
      }
    """
    Then the response status code should be 200

  Scenario: Removing existing destinations
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PUT" request to "/api/{version}/organization/destinations/" with body:
     """
      {
        "publish_destination":{
          "publishDestinations":[
            {
              "tenant":"123abc",
              "route":5,
              "fbia":false,
              "published":false,
              "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
            },
            {
              "tenant":"123abc",
              "route":6,
              "fbia":false,
              "published":false,
              "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bd"
            }
          ]
        }
      }
    """
    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "PUT" request to "/api/{version}/organization/destinations/" with body:
     """
      {
        "publish_destination":{
          "publishDestinations":[
            {
              "tenant":"123abc",
              "route":5,
              "fbia":false,
              "published":false,
              "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
            }
          ]
        }
      }
    """
    Then the response status code should be 200