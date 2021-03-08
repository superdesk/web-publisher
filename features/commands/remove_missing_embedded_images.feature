@organization_commands
@disable-fixtures
Feature: Removing missing embedded images from article bodies
  In order to fix google amp validation
  As a console command
  I want to be able to find articles with not converted article body images and remove them from article body

  Scenario: Reprocessing packages with commands:
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following Users:
      | username   | email                      | token      | password | role                |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   |

    Given the following organization publishing rule:
    """
      {
        "name":"Test rule",
        "description":"Test rule description",
        "priority":1,
        "expression":"true == true",
        "configuration":[
          {
            "key":"destinations",
            "value":[
              {
                "tenant":"123abc"
              }
            ]
          }
        ]
      }
    """

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

    Given the following tenant publishing rule:
    """
      {
          "name":"Test tenant rule",
          "description":"Test tenant rule description",
          "priority":1,
          "expression":"article.getPackage().getLanguage() == 'en'",
          "configuration":[
            {
              "key":"route",
              "value":1
            },
            {
              "key":"published",
              "value":true
            }
          ]
       }
    """

    Given the following Package ninjs:
    """
      {
          "language":"en","headline":"Test Package","version":"2","guid":"16e111d5","priority":6,"type":"text",
          "authors":[{"name":"Tom Doe","role":"editor"}],
          "body_html":"<p>some html unique 123 body <a href=\"www.example.com/images/12345.jpeg\"><img src=\"www.example.com/images/12345.jpeg\" /></a> <a href=\"www.example.com/good_images/12345.jpeg\"><img src=\"www.example.com/good_images/12345.jpeg\" /></a></p>"
      }
    """
    Given default tenant with code "123abc"

    When I run the "swp:fixer:remove-missing-embedded-images" command with options:
      | --dry-run    | true                   |
      | term         | www.example.com/images |
      | parent       | a                      |
      | --tenant     | 123abc                 |
      | --limit      | 10                     |
    Then the command output should be "Done. In total processed 1 articles"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/test-package"
    Then the response status code should be 200
    And the JSON node "body" should be equal to '<p>some html unique 123 body  <a href="www.example.com/good_images/12345.jpeg"><img src="www.example.com/good_images/12345.jpeg"></a></p>'
