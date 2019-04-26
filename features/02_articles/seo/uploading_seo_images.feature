@articles
@disable-fixtures
Feature: Adding article SEO metadata
  In order to customize how my content is rendered in social media
  As a HTTP Client
  I want to be able to define custom article SEO metadata for social media platform

  Scenario: Uploading SEO meta image
    Given the following Tenants:
      | organization | name | subdomain | domainName | enabled | default  | themeName      |
      | Default      | test |           | localhost  | true    | true     | swp/test-theme |

    Given the following Routes:
      |  name | type       | slug | templateName       |
      |  test | collection | test | seo_twig.html.twig |

    Given the following Articles:
      | title               | route      | status    | publishable  |
      | Lorem               | test       | published | true         |

    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/upload/seo_image/lorem" with parameters:
      | key                | value      |
      | metaMediaFile      | @logo.png  |
    Then the response status code should be 201

#    Then I am authenticated as "test.user"
#    Then I send a "POST" request to "/api/v2/content/articles/lorem/upload" with parameters:
#      | key             | value      |
#      | ogImageFile     | @logo.png  |
#    Then the response status code should be 201
#
#    Then I am authenticated as "test.user"
#    Then I send a "POST" request to "/api/v2/content/articles/lorem/upload" with parameters:
#      | key                   | value      |
#      | twitterImageFile      | @logo.png  |
#    Then the response status code should be 201
#
    When I go to "/test/lorem"
    Then the response status code should be 200
    And the response should contain "http://localhost/media/seo/0123456789abc.png"
