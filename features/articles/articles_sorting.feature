@articles
Feature: List sorted articles
  In order to see if sorting by underscore, camelCase properties works
  As a HTTP Client
  I want to be able to sort articles by underscore and camelCase properties

  Scenario: Sort articles by underscore property
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[updated_at]=desc"
    Then the response status code should be 200

  Scenario: Sort articles by camelCase property
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[updatedAt]=desc"
    Then the response status code should be 200

  Scenario: Sort articles by fake property
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[fake]=desc"
    Then the response status code should be 500

  Scenario: Sort articles by page views number property
    Given I run "fos:elastica:populate --env=test" command

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[article_statistics.page_views_number]=desc"
    Then the response status code should be 200
    And the JSON node "_embedded._items[0].id" should be equal to 2
    And the JSON node "_embedded._items[0].article_statistics.page_views_number" should be equal to 30
    And the JSON node "_embedded._items[1].id" should be equal to 1
    And the JSON node "_embedded._items[1].article_statistics.page_views_number" should be equal to 20
    And the JSON node "_embedded._items[2].id" should be equal to 3
    And the JSON node "_embedded._items[2].article_statistics.page_views_number" should be equal to 10
    And the JSON node "_embedded._items[3].id" should be equal to 4
    And the JSON node "_embedded._items[3].article_statistics.page_views_number" should be equal to 5
    And the JSON node "_embedded._items[4].id" should be equal to 5
    And the JSON node "_embedded._items[4].article_statistics.page_views_number" should be equal to 0

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/?sorting[article_statistics.page_views_number]=asc"
    Then the response status code should be 200
    And the JSON node "_embedded._items[4].id" should be equal to 2
    And the JSON node "_embedded._items[4].article_statistics.page_views_number" should be equal to 30
    And the JSON node "_embedded._items[3].id" should be equal to 1
    And the JSON node "_embedded._items[3].article_statistics.page_views_number" should be equal to 20
    And the JSON node "_embedded._items[2].id" should be equal to 3
    And the JSON node "_embedded._items[2].article_statistics.page_views_number" should be equal to 10
    And the JSON node "_embedded._items[1].id" should be equal to 4
    And the JSON node "_embedded._items[1].article_statistics.page_views_number" should be equal to 5
    And the JSON node "_embedded._items[0].id" should be equal to 5
    And the JSON node "_embedded._items[0].article_statistics.page_views_number" should be equal to 0
