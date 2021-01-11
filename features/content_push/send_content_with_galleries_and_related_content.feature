@disable-fixtures
Feature: Check if the featuremedia metadata are set properly

  Scenario: Preview article with media
    Given the following Tenants:
      | organization | name | code   | subdomain | domain_name | enabled | default |
      | Default      | test | 123abc |           | localhost   | true    | true    |

    Given the following Users:
      | username   | email                      | token      | password | role                | enabled |
      | test.user  | test.user@sourcefabric.org | test_user: | testPassword  | ROLE_INTERNAL_API   | true    |

    Given the following Package ninjs:
    """
    {
      "authors": [
        {
          "name": "Boris Ja\u0161ovi\u0107",
          "role": "writer",
          "code": "5c18b6d7b78968205988d7f7",
          "biography": ""
        }
      ],
      "copyrightholder": "",
      "version": "3",
      "profile": "News",
      "firstcreated": "2019-09-06T12:43:30+0000",
      "readtime": 2,
      "description_text": "She laughed and took his arm. \"You dear old Jim, you talk as if you were a hundred. Some day you will be in love yourself.",
      "keywords": [
        "Lit ipsum",
        "testing",
        "cropping imges"
      ],
      "guid": "urn:newsml:vijesti-staging-api.superdesk.pro:2019-09-06T14:43:30.429099:e4822d08-7ac0-49f0-a987-96dfb1d9ee37xx2",
      "headline": "In an open carriage with two ladies Dorian Gray drove past",
      "description_html": "<p>She laughed and took his arm. \"You dear old Jim, you talk as if you were a hundred. Some day you will be in love yourself.<\/p>",
      "firstpublished": "2019-09-09T08:36:11+0000",
      "priority": 5,
      "language": "en",
      "type": "text",
      "body_html": "<p>Then you will know what it is. Don't look so sulky. Surely you should be glad to think that, though you are going away, you leave me happier than I have ever been before. Life has been hard for us both, terribly hard and difficult. But it will be different now. You are going to a new world, and I have found one. Here are two chairs; let us sit down and see the smart people go by.\"<\/p>\n<p>They took their seats amidst a crowd of watchers. The tulip-beds across the road flamed like throbbing rings of fire. A white dust--tremulous cloud of orris-root it seemed--hung in the panting air. The brightly coloured parasols danced and dipped like monstrous butterflies.<\/p>\n<p>She made her brother talk of himself, his hopes, his prospects. He spoke slowly and with effort. They passed words to each other as players at a game pass counters. Sibyl felt oppressed. She could not communicate her joy. A faint smile curving that sullen mouth was all the echo she could win. After some time she became silent. Suddenly she caught a glimpse of golden hair and laughing lips, and in an open carriage with two ladies Dorian Gray drove past.<\/p>\n<p>She started to her feet. \"There he is!\" she cried.<\/p>\n<p>\"Who?\" said Jim Vane.<\/p>\n<p>\"Prince Charming,\" she answered, looking after the victoria.<\/p>\n<p>He jumped up and seized her roughly by the arm. \"Show him to me. Which is he? Point him out. I must see him!\" he exclaimed; but at that moment the Duke of Berwick's four-in-hand came between, and when it had left the space clear, the carriage had swept out of the park.<\/p>\n<p>\"He is gone,\" murmured Sibyl sadly. \"I wish you had seen him.\"<\/p>\n<p>\"I wish I had, for as sure as there is a God in heaven, if he ever does you any wrong, I shall kill him.\"<\/p>\n<p>She looked at him in horror. He repeated his words. They cut the air like a dagger. The people round began to gape. A lady standing close to her tittered.<\/p>\n<p>\"Come away, Jim; come away,\" she whispered. He followed her doggedly as she passed through the crowd. He felt glad at what he had said.<\/p>\n<p>When they reached the Achilles Statue, she turned round. There was pity in her eyes that became laughter on her lips. She shook her head at him. \"You are foolish, Jim, utterly foolish; a bad-tempered boy, that is all. How can you say such horrible things? You don't know what you are talking about. You are simply jealous and unkind. Ah! I wish you would fall in love. Love makes people good, and what you said was wicked.\"<\/p>",
      "service": [
        {
          "name": "Sport",
          "code": "sport"
        }
      ],
      "versioncreated": "2019-09-09T08:36:12+0000",
      "extra": {
        "overtitle": "Lit ipsum"
      },
      "copyrightnotice": "",
      "extra_items": {
        "related_articles": {
          "items": [
            {
              "authors": [
                {
                  "role": "writer",
                  "name": "Blagoje Grahovac",
                  "code": "5c288debb789682059898034",
                  "biography": ""
                }
              ],
              "copyrightholder": "",
              "version": "2",
              "profile": "News",
              "firstcreated": "2019-09-04T13:34:03+0000",
              "readtime": 2,
              "description_text": "I courtsied to him twice ere he would speak to me. When he did, I began to apologize for having disappointed him",
              "keywords": [
                "Lit ipsum",
                "testing",
                "NinJS"
              ],
              "guid": "urn:newsml:vijesti-staging-api.superdesk.pro:2019-09-04T15:34:03.850112:e58f7c15-9a9e-43d9-b095-6c3df67249c9",
              "headline": "At the pump-room I saw Mr. Macartney",
              "description_html": "<p>I courtsied to him twice ere he would speak to me. When he did, I began to apologize for having disappointed him<\/p>",
              "firstpublished": "2019-09-04T13:37:13+0000",
              "priority": 6,
              "language": "en",
              "type": "text",
              "body_html": "<p>At the pump-room I saw Mr. Macartney; I courtsied to him twice ere he would speak to me. When he did, I began to apologize for having disappointed him; but I did not find it very easy to excuse myself, as Lord Orville's eyes, with an expression of anxiety that distressed me, turned from him to me, and me to him, every word I spoke. Convinced, however, that I had really trifled with Mr. Macartney, I scrupled not to beg his pardon. He was then not merely appeased, but even grateful.<\/p>\n<p>He requested me to see him to-morrow; but I had not the folly to be again guilty of an indiscretion; which had already caused me so much uneasiness; and therefore I told him frankly, that it was not in my power at present to see him but by accident; and, to prevent his being offended, I hinted to him the reason I could not receive him as I wished to do.<\/p>\n<p>When I had satisfied both him and myself upon this subject, I turned to Lord Orville, and saw, with concern, the gravity of his countenance. I would have spoken to him, but knew not how; I believe, however, he read my thoughts; for, in a little time, with a sort of serious smile, he said, \"Does not Mr. Macartney complain of his disappointment?\"<\/p>\n<p>\"Not much, my Lord.\"<\/p>\n<p>\"And how have you appeased him?\" Finding I hesitated what to answer, \"Am I not your brother?\" continued he, \"and must I not enquire into your affairs?\"<\/p>\n<p>\"Certainly, my Lord,\" said I, laughing. \"I only wish it were better worth your Lordship's while.\"<\/p>\n<p>\"Let me, then, make immediate use of my privilege. When shall you see Mr. Macartney again?\"<\/p>\n<p>\"Indeed, my Lord, I can't tell.\"<\/p>\n<p>\"But,-do you know that I shall not suffer my sister to make a private appointment?\"<\/p>\n<p>\"Pray, my Lord,\" cried I earnestly, \"use that word no more! Indeed you shock me extremely.\"<\/p>\n<p>\"That would I not do for the world,\" cried he, \"yet you know not how warmly, how deeply I am interested, not only in all your concerns, but in all your actions.\"<\/p>\n<p>This speech-the most particular one Lord Orville had ever made to me, ended our conversation at that time; for I was too much struck by it to make any answer.<\/p>\n<p>Soon after, Mr. Macartney, in a low voice, intreated me not to deny him the gratification of returning the money. While he was speaking, the young lady I saw yesterday at the assembly, with the large party, entered the pump-room. Mr. Macartney turned as pale as death, his voice faultered, and he seemed not to know what he said. I was myself almost equally disturbed, by the crowd of confused ideas that occurred to me. Good Heaven! thought I, why should he be thus agitated?-is it possible this can be the young lady he loved?<\/p>",
              "service": [
                {
                  "name": "Sport",
                  "code": "sport"
                }
              ],
              "versioncreated": "2019-09-04T13:36:18+0000",
              "extra": {
                "overtitle": "Lit ipsum"
              },
              "copyrightnotice": "",
              "wordcount": 487,
              "usageterms": "",
              "annotations": [

              ],
              "charcount": 2598,
              "source": "vijesti",
              "urgency": 3,
              "genre": [
                {
                  "name": "Article (news)",
                  "code": "Article"
                }
              ],
              "pubstatus": "usable"
            }
          ],
          "type": "related_content"
        }
      },
      "wordcount": 450,
      "usageterms": "",
      "annotations": [

      ],
      "charcount": 2392,
      "source": "vijesti",
      "pubstatus": "usable",
      "associations": {
        "featuremedia": {
          "renditions": {
            "original": {
              "href": "http://localhost:3000/api/upload/20181218121220_a3c082eea33af42478d4fe57b2ae97702b378faa481e5825028f821ee37d2d62/raw?_schema=http",
              "height":1280,
              "width":960,
              "media":"20181218121220/a3c082eea33af42478d4fe57b2ae97702b378faa481e5825028f821ee37d2d62.jpg",
              "mimetype": "image\/jpeg"
            }
          },
          "copyrightholder": "",
          "version": "2",
          "versioncreated": "2019-09-06T12:46:37+0000",
          "firstcreated": "2019-09-06T12:46:35+0000",
          "copyrightnotice": "",
          "body_text": "Kuma debela",
          "description_text": "Kuma debela",
          "usageterms": "",
          "guid": "tag:vijesti-staging-api.superdesk.pro:2019:98a7ae93-f722-4d34-8900-c1b76fbc9e48",
          "headline": "Kuma debela",
          "mimetype": "image\/jpeg",
          "priority": 6,
          "source": "",
          "language": "en",
          "urgency": 3,
          "genre": [
            {
              "name": "Article (news)",
              "code": "Article"
            }
          ],
          "type": "picture",
          "pubstatus": "usable"
        },
        "related_articles--1": {
          "authors": [
            {
              "role": "writer",
              "name": "Blagoje Grahovac",
              "code": "5c288debb789682059898034",
              "biography": ""
            }
          ],
          "copyrightholder": "",
          "version": "2",
          "profile": "News",
          "firstcreated": "2019-09-04T13:34:03+0000",
          "readtime": 2,
          "description_text": "I courtsied to him twice ere he would speak to me. When he did, I began to apologize for having disappointed him",
          "keywords": [
            "Lit ipsum",
            "testing",
            "NinJS"
          ],
          "guid": "urn:newsml:vijesti-staging-api.superdesk.pro:2019-09-04T15:34:03.850112:e58f7c15-9a9e-43d9-b095-6c3df67249c9",
          "headline": "At the pump-room I saw Mr. Macartney",
          "description_html": "<p>I courtsied to him twice ere he would speak to me. When he did, I began to apologize for having disappointed him<\/p>",
          "firstpublished": "2019-09-04T13:37:13+0000",
          "priority": 6,
          "language": "en",
          "type": "text",
          "body_html": "<p>At the pump-room I saw Mr. Macartney; I courtsied to him twice ere he would speak to me. When he did, I began to apologize for having disappointed him; but I did not find it very easy to excuse myself, as Lord Orville's eyes, with an expression of anxiety that distressed me, turned from him to me, and me to him, every word I spoke. Convinced, however, that I had really trifled with Mr. Macartney, I scrupled not to beg his pardon. He was then not merely appeased, but even grateful.<\/p>\n<p>He requested me to see him to-morrow; but I had not the folly to be again guilty of an indiscretion; which had already caused me so much uneasiness; and therefore I told him frankly, that it was not in my power at present to see him but by accident; and, to prevent his being offended, I hinted to him the reason I could not receive him as I wished to do.<\/p>\n<p>When I had satisfied both him and myself upon this subject, I turned to Lord Orville, and saw, with concern, the gravity of his countenance. I would have spoken to him, but knew not how; I believe, however, he read my thoughts; for, in a little time, with a sort of serious smile, he said, \"Does not Mr. Macartney complain of his disappointment?\"<\/p>\n<p>\"Not much, my Lord.\"<\/p>\n<p>\"And how have you appeased him?\" Finding I hesitated what to answer, \"Am I not your brother?\" continued he, \"and must I not enquire into your affairs?\"<\/p>\n<p>\"Certainly, my Lord,\" said I, laughing. \"I only wish it were better worth your Lordship's while.\"<\/p>\n<p>\"Let me, then, make immediate use of my privilege. When shall you see Mr. Macartney again?\"<\/p>\n<p>\"Indeed, my Lord, I can't tell.\"<\/p>\n<p>\"But,-do you know that I shall not suffer my sister to make a private appointment?\"<\/p>\n<p>\"Pray, my Lord,\" cried I earnestly, \"use that word no more! Indeed you shock me extremely.\"<\/p>\n<p>\"That would I not do for the world,\" cried he, \"yet you know not how warmly, how deeply I am interested, not only in all your concerns, but in all your actions.\"<\/p>\n<p>This speech-the most particular one Lord Orville had ever made to me, ended our conversation at that time; for I was too much struck by it to make any answer.<\/p>\n<p>Soon after, Mr. Macartney, in a low voice, intreated me not to deny him the gratification of returning the money. While he was speaking, the young lady I saw yesterday at the assembly, with the large party, entered the pump-room. Mr. Macartney turned as pale as death, his voice faultered, and he seemed not to know what he said. I was myself almost equally disturbed, by the crowd of confused ideas that occurred to me. Good Heaven! thought I, why should he be thus agitated?-is it possible this can be the young lady he loved?<\/p>",
          "service": [
            {
              "name": "Sport",
              "code": "sport"
            }
          ],
          "versioncreated": "2019-09-04T13:36:18+0000",
          "extra": {
            "overtitle": "Lit ipsum"
          },
          "copyrightnotice": "",
          "wordcount": 487,
          "usageterms": "",
          "annotations": [

          ],
          "charcount": 2598,
          "source": "vijesti",
          "urgency": 3,
          "genre": [
            {
              "name": "Article (news)",
              "code": "Article"
            }
          ],
          "pubstatus": "usable"
        }
      }
    }
    """

    Given default tenant with code "123abc"
    Given the following Routes:
      |  name      | type       | slug     |
      |  Tech News | collection | technews |

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/1/publish/" with body:
     """
      {
          "destinations":[
            {
              "tenant":"123abc",
              "published":true,
              "route": 1
            }
          ]
      }
     """
    Then the response status code should be 201

    Given I am authenticated as "test.user"
    Then I send a "GET" request to "/api/v2/content/slideshows/1"
    Then the response status code should be 200
    And the JSON node "total" should be equal to 0
