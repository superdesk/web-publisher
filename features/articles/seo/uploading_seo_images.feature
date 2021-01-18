@articles
@disable-fixtures
Feature: Adding article SEO metadata
  In order to customize how my content is rendered in social media
  As a HTTP Client
  I want to be able to define custom article SEO metadata for social media platform

  Scenario: Uploading SEO meta image
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      |  code   |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme | 123abc  |

    Given the following Routes:
      |  name | type       | slug | templateName       |
      |  test | collection | test | seo_twig.html.twig |

    Given the following Articles:
      | title               | route      | status    | isPublishable  |
      | Lorem               | test       | published | true           |

    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "multipart/form-data"
    Then I send a "POST" request to "/api/v2/upload/seo_image/lorem" with parameters:
      | key                  | value      |
      | meta_media_file      | @logo.png  |
    Then the response status code should be 201

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "multipart/form-data"
    Then I send a "POST" request to "/api/v2/upload/seo_image/lorem" with parameters:
      | key               | value      |
      | og_media_file     | @logo.png  |
    Then the response status code should be 201

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "multipart/form-data"
    Then I send a "POST" request to "/api/v2/upload/seo_image/lorem" with parameters:
      | key                     | value      |
      | twitter_media_file      | @logo.png  |
    Then the response status code should be 201

    When I go to "/test/lorem"
    Then the response status code should be 200
    And the response should contain "http://localhost/seo/media/0123456789abc.png"

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/lorem"
    Then the response status code should be 200
    And the JSON node "seo_metadata._links.meta_media_url.href" should be equal to "http://localhost/seo/media/0123456789abc.png"
    And the JSON node "seo_metadata._links.og_media_url.href" should be equal to "http://localhost/seo/media/0123456789abc.png"
    And the JSON node "seo_metadata._links.twitter_media_url.href" should be equal to "http://localhost/seo/media/0123456789abc.png"

    When I send a "GET" request to "http://localhost/seo/media/0123456789abc.png"
    Then the response status code should be 200
