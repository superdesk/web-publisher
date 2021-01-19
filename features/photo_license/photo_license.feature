@preview
@disable-fixtures
Feature: As an editor, I want to be able to set the photo license for the featured image and display it on the frontend

  Scenario: Submit article with the photo and the license
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following Users:
      | username   | email                      | token      | plainPassword | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given the following Package ninjs:
    """
    {
       "urgency":2,
       "body_html":"<p>There was a long silence, during which Holmes leaned his chin upon his hands and stared into the crackling fire.</p>\n<p>\"This is a very deep business,\" he said at last. \"There are a thousand details which I should desire to know before I decide upon our course of action. Yet we have not a moment to lose. If we were to come to Stoke Moran to-day, would it be possible for us to see over these rooms without the knowledge of your stepfather?\"</p>\n<p>\"As it happens, he spoke of coming into town to-day upon some most important business. It is probable that he will be away all day, and that there would be nothing to disturb you. We have a housekeeper now, but she is old and foolish, and I could easily get her out of the way.\"</p>\n<p>\"Excellent. You are not averse to this trip, Watson?\"</p>\n<p>\"By no means.\"</p>\n<p>\"Then we shall both come. What are you going to do yourself?\"</p>\n<p>\"I have one or two things which I would wish to do now that I am in town. But I shall return by the twelve o'clock train, so as to be there in time for your coming.\"</p>\n<p>\"And you may expect us early in the afternoon. I have myself some small business matters to attend to. Will you not wait and breakfast?\"</p>\n<p>\"No, I must go. My heart is lightened already since I have confided my trouble to you. I shall look forward to seeing you again this afternoon.\" She dropped her thick black veil over her face and glided from the room.</p>\n<p>\"And what do you think of it all, Watson?\" asked Sherlock Holmes, leaning back in his chair.</p>\n<p>\"It seems to me to be a most dark and sinister business.\"</p>",
       "guid":"urn:newsml:superdesk.pro:2020-12-29T13:36:14.368485:df55fc66-ef91-43e4-a887-ab25ae123146",
       "versioncreated":"2020-12-29T12:41:42+0000",
       "priority":5,
       "firstcreated":"2020-12-29T12:36:14+0000",
       "headline":"Holmes leaned his chin upon his hands",
       "wordcount":295,
       "firstpublished":"2020-12-30T10:43:42+0000",
       "associations":{
          "featuremedia":{
             "urgency":3,
             "renditions":{
                "original":{
                   "width":2048,
                   "mimetype":"image/jpeg",
                   "poi":{
                      "x":1228,
                      "y":586
                   },
                   "media":"20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg",
                   "height":1365,
                   "href":"http://localhost:3000/api/upload/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea/raw"
                }
             },
             "guid":"tag:superdesk.pro:2020:b68856ef-e0b2-4d64-a9d0-f433c8dde5c3",
             "subject":[
                {
                   "code":"https://creativecommons.org/licenses/by-nc-sa/4.0/",
                   "scheme":"photo_license",
                   "name":"CC BY-NC-SA 4.0"
                }
             ],
             "versioncreated":"2020-12-29T12:38:50+0000",
             "priority":6,
             "version":"3",
             "firstcreated":"2020-12-29T12:38:47+0000",
             "headline":"downtown Sofia",
             "type":"picture",
             "language":"en",
             "firstpublished":"2020-12-30T10:43:42+0000",
             "byline":"Ljub",
             "source":"test",
             "body_text":"downtown Sofia",
             "usageterms":"Time Restricted",
             "mimetype":"image/jpeg",
             "description_text":"Downtown Sofia",
             "copyrightholder":"Studio V.",
             "pubstatus":"usable",
             "genre":[
                {
                   "code":"Article",
                   "name":"Article (news)"
                }
             ]
          }
       },
       "readtime":1,
       "copyrightholder":"",
       "usageterms":"",
       "copyrightnotice":"",
       "service":[
          {
             "code":"news",
             "name":"News"
          }
       ],
       "type":"text",
       "keywords":[
          "Test"
       ],
       "language":"en",
       "charcount":1507,
       "source":"test",
       "description_html":"<p>The lady coloured deeply and covered over her injured wrist. \"He is a hard man,\" she said, \"and perhaps he hardly knows his own strength.\"</p>",
       "version":"5",
       "annotations":[

       ],
       "profile":"NewsFeatureInterview",
       "subject":[
          {
             "code":"news",
             "scheme":"atype",
             "name":"News"
          }
       ],
       "description_text":"The lady coloured deeply and covered over her injured wrist. \"He is a hard man,\" she said, \"and perhaps he hardly knows his own strength.\"",
       "authors":[
          {
             "code":"5f87fe4980e0ef91bc7e405c",
             "biography":"",
             "role":"writer",
             "name":"Doe Tom"
          }
       ],
       "pubstatus":"usable"
    }
    """

    And I publish the submitted package "urn:newsml:superdesk.pro:2020-12-29T13:36:14.368485:df55fc66-ef91-43e4-a887-ab25ae123146":
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true
            }
          ]
      }
     """

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/holmes-leaned-his-chin-upon-his-hands"
    And the JSON node "feature_media.license.name" should be equal to "CC BY-NC-SA 4.0"
    And the JSON node "feature_media.license.url" should be equal to "https://creativecommons.org/licenses/by-nc-sa/4.0/"

    Given default tenant with code "123abc"
    And I render a template with content:
     """
       {% gimme article with {id: 1} %}
          <figure>
              {{ gimme.article.featureMedia.license.name }}
              {{ gimme.article.featureMedia.license.url }}
          </figure>
      {% endgimme %}
     """
    Then rendered template should contain "CC BY-NC-SA 4.0"
    Then rendered template should contain "https://creativecommons.org/licenses/by-nc-sa/4.0/"
