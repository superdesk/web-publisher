@targeted_publishing
Feature: Managing targeted publishing destinations

  Scenario: Creating a new targeted publishing destinations
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/organization/destinations/" with body:
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

  Scenario: Removing existing destinations
#    Given I am authenticated as "test.user"
#    When I add "Content-Type" header equal to "application/json"
#    And I send a "POST" request to "/api/v1/organization/destinations/" with body:
#     """
#      {
#        "publish_destination":{
#          "publishDestinations":[
#            {
#              "tenant":"123abc",
#              "route":5,
#              "fbia":false,
#              "published":false,
#              "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
#            },
#            {
#              "tenant":"123abc",
#              "route":6,
#              "fbia":false,
#              "published":false,
#              "packageGuid": "urn:newsml:sd-master.test.superdesk.org:2022-09-19T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bd"
#            }
#          ]
#        }
#      }
#    """
#    Then the response status code should be 200
    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v1/organization/destinations/" with body:
     """
      {
        "publish_destination":{
          "tenant":"123abc",
          "route":5,
          "fbia":false,
          "published":false,
          "packageGuid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0"
        }
      }
    """
    Then the response status code should be 200
    And the JSON node "published" should be false
    And the JSON node "fbia" should be false
