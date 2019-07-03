@webp
@disable-fixtures
Feature: Converting images to webp format and rendering them for reader
  In order to work with webp images i need to create them from provided images and render to readers
  As a user
  I want to be able to send ninjs package and see webp image in article output

  Scenario: Pushing package with :
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

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

    Given default tenant with code "123abc"
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
        "associations":{
          "featuremedia":{
            "subject":[
              {
                "code":"05004000",
                "name":"preschool"
              }
            ],
            "type":"picture",
            "usageterms":"indefinite-usage",
            "priority":6,
            "byline":"Ljub. Z. Rankovi\u0107",
            "urgency":3,
            "language":"en",
            "headline":"Smoke on the water",
            "versioncreated":"2017-01-11T14:52:05+0000",
            "description_text":"Smoke on the water on River Gradac\u00a0",
            "guid":"tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09",
            "body_text":"Gradac",
            "version":"5",
            "renditions":{
              "viewImage":{
                "width":640,
                "mimetype":"image/jpeg",
                "poi":{
                  "x":384,
                  "y":183
                },
                "media":"20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg",
                "height":426,
                "href":"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http"
              },
              "baseImage":{
                "width":1400,
                "mimetype":"image/jpeg",
                "poi":{
                  "x":840,
                  "y":401
                },
                "media":"20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg",
                "height":933,
                "href":"http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http"
              },
              "original":{
                "width":2048,
                "mimetype":"image/jpeg",
                "poi":{
                  "x":1228,
                  "y":586
                },
                "media":"20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg",
                "height":1365,
                "href":"http://localhost:3000/api/upload/1234567890987654321c/raw?_schema=http"
              }
            }
          }
        }
      }
    """

    Given default tenant with code "123abc"
    And I render a template with content:
     """
       {% gimme article with {id: 1} %}
          <figure>
              {% gimme rendition with { 'media': gimme.article.featureMedia, 'name': 'original' } %}
                <picture>
                    {% if rendition.isConvertedToWebp %}
                    <source srcset="{{ path(rendition, { webp: true }) }}" type="image/webp" width="2048" height="1365">
                    {% endif %}
                    <img src="{{ path(rendition) }}" alt="{{ imgCaption }}" width="2048" height="1365" />
                </picture>
              {% endgimme %}
          </figure>
           <figure>
              {% gimme rendition with { 'media': gimme.article.featureMedia, 'name': 'viewImage' } %}
                <picture>
                    {% if rendition.isConvertedToWebp %}
                    <source srcset="{{ path(rendition, { webp: true }) }}" type="image/webp" width="2048" height="1365">
                    {% endif %}
                    <img src="{{ path(rendition) }}" alt="{{ imgCaption }}" width="2048" height="1365" />
                </picture>
              {% endgimme %}
          </figure>
           <figure>
              {% gimme rendition with { 'media': gimme.article.featureMedia, 'name': 'baseImage' } %}
                <picture>
                    {% if rendition.isConvertedToWebp %}
                    <source srcset="{{ path(rendition, { webp: true }) }}" type="image/webp" width="2048" height="1365">
                    {% endif %}
                    <img src="{{ path(rendition) }}" alt="{{ imgCaption }}" width="2048" height="1365" />
                </picture>
              {% endgimme %}
          </figure>
      {% endgimme %}
     """
    Then rendered template should contain "http://localhost/uploads/swp/123456/media/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.webp"
    Then rendered template should contain "http://localhost/uploads/swp/123456/media/20170111140132_979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.png"
    Then rendered template should contain "http://localhost/uploads/swp/123456/media/20170111140132_3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.webp"
    Then rendered template should contain "http://localhost/uploads/swp/123456/media/20170111140132_3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.png"
    Then rendered template should contain "http://localhost/uploads/swp/123456/media/20170111140132_828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.webp"
    Then rendered template should contain "http://localhost/uploads/swp/123456/media/20170111140132_828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.png"
