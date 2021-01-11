@articles
@disable-fixtures
Feature: Adding article SEO metadata
  In order to customize how my content is rendered in social media
  As a HTTP Client
  I want to be able to define custom article SEO metadata for social media platform

  Scenario: Adding SEO metadata to the article
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      | code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc |

    Given the following Routes:
      |  name | type       | slug | templateName       |
      |  test | collection | test | seo_twig.html.twig |

    Given the following Articles:
      | title               | route      | status    | isPublishable  |
      | Lorem               | test       | published | true           |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/articles/lorem" with body:
     """
      {
          "seo_metadata": {
              "meta_title": "This is my meta title",
              "meta_description": "This is my meta description",
              "og_title": "This is my og title",
              "og_description": "This is my og description",
              "twitter_title": "This is my twitter title",
              "twitter_description": "This is my twitter description"
          }
      }
     """
    Then the response status code should be 200
    And the JSON node "seo_metadata.meta_title" should be equal to "This is my meta title"
    And the JSON node "seo_metadata.meta_description" should be equal to "This is my meta description"
    And the JSON node "seo_metadata.og_title" should be equal to "This is my og title"
    And the JSON node "seo_metadata.og_description" should be equal to "This is my og description"
    And the JSON node "seo_metadata.twitter_title" should be equal to "This is my twitter title"
    And the JSON node "seo_metadata.twitter_description" should be equal to "This is my twitter description"

    When I go to "/test/lorem"
    Then the response status code should be 200
    And the response should contain "This is my og title"
    And the response should contain "This is my og description"
    And the response should contain "This is my meta title"
    And the response should contain "This is my meta description"
    And the response should contain "This is my twitter title"
    And the response should contain "This is my twitter description"

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/articles/lorem" with body:
     """
      {
          "seo_metadata": {
              "meta_title": "",
              "meta_description": "This is my meta description",
              "og_title": "This is my og title",
              "og_description": "This is my og description",
              "twitter_title": "This is my twitter title",
              "twitter_description": "This is my twitter description"
          }
      }
     """
    Then the response status code should be 200
    And the JSON node "seo_metadata.meta_title" should be equal to ""
    And the JSON node "seo_metadata.meta_description" should be equal to "This is my meta description"
    And the JSON node "seo_metadata.og_title" should be equal to "This is my og title"
    And the JSON node "seo_metadata.og_description" should be equal to "This is my og description"
    And the JSON node "seo_metadata.twitter_title" should be equal to "This is my twitter title"
    And the JSON node "seo_metadata.twitter_description" should be equal to "This is my twitter description"

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/content/articles/lorem" with body:
     """
      {
          "seo_metadata": {
              "meta_title": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nisi odio, tristique non justo tristique, feugiat finibus elit. Donec euismod ante quis sollicitudin fringilla. Mauris efficitur sodales sed."
          }
      }
     """
    Then the response status code should be 400

