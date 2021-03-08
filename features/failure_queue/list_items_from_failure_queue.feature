@failure-queue
@disable-fixtures
Feature: Export articles analytics report
  In order to debug what failed in the async processing
  As a HTTP Client
  I want to be able to see a list of failed entries

  Background:
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      | code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc |

    Given the following Users:
      | username   | email                      | token      | password | role                |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   |

    Given the failed items exist in the failure queue

  Scenario: Get entries from the failure queue
    Given I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "GET" request to "/api/v2/failed_queue/?max=5"
    Then the response status code should be 200
    And the JSON should be equal to:
    """
    [
       {
          "id":1,
          "class":"SWP\\Bundle\\CoreBundle\\MessageHandler\\Message\\ContentPushMessage",
          "failed_at":"2020-02-18T11:00:00+00:00",
          "error_message":"error",
          "transport":"messenger.transport.failed",
          "redeliveries":[
             "2020-02-18T11:00:00+00:00"
          ],
          "message":{
             "tenant":1,
             "content":"some content"
          },
          "exception_stacktrace":"stack trace exception"
       }
    ]
    """
