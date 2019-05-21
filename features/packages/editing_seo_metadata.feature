@packages
Feature: Editing existing SEO metadata

  Scenario: Editing existing package SEO metadata
    Given I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/seo/" with parameters:
      | key                  | value                                                           |
      | metaMediaFile        | @logo.png                                                       |
      | metaTitle            | This is my meta title                                           |
      | metaDescription      | This is my meta description                                     |
      | ogTitle              | This is my og title                                             |
      | ogDescription        | This is my og description                                       |
      | twitterTitle         | This is my twitter title                                        |
      | twitterDescription   | This is my twitter description                                  |
      | ogMediaFile          | @logo.png                                                       |
      | twitterMediaFile     | @logo.png                                                       |
      | packageGuid          | 2018-01-18T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc |
    Then the response status code should be 200

    And the JSON node "_links.meta_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "_links.og_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "_links.twitter_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "meta_title" should be equal to "This is my meta title"
    And the JSON node "meta_description" should be equal to "This is my meta description"
    And the JSON node "og_title" should be equal to "This is my og title"
    And the JSON node "og_description" should be equal to "This is my og description"
    And the JSON node "twitter_title" should be equal to "This is my twitter title"
    And the JSON node "twitter_description" should be equal to "This is my twitter description"

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/seo/2018-01-18T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc"
    Then the response status code should be 200
    And the JSON node "_links.meta_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "_links.og_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "_links.twitter_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "meta_title" should be equal to "This is my meta title"
    And the JSON node "meta_description" should be equal to "This is my meta description"
    And the JSON node "og_title" should be equal to "This is my og title"
    And the JSON node "og_description" should be equal to "This is my og description"
    And the JSON node "twitter_title" should be equal to "This is my twitter title"
    And the JSON node "twitter_description" should be equal to "This is my twitter description"

    And I am authenticated as "test.user"
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/api/v2/content/push" with body:
    """
    {
      "language": "en",
      "byline":"John Jones",
      "source":"Sourcefabric",
      "type":"text",
      "description_text":"Lorem ipsum abstract",
      "guid":"2018-01-18T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc",
      "profile":"Article",
      "subject":[
        {
          "code":"01001000",
          "name":"archaeology"
        }
      ],
      "wordcount":3,
      "urgency":3,
      "authors":[
        {
          "biography":"ed bio",
          "name":"ed",
          "role":"subeditor"
        },
        {
          "biography":"not dead yet",
          "name":"vincer vincer",
          "role":"subeditor",
          "avatar_url":"http://via.placeholder.com/95x95.jpg"
        }
      ],
      "copyrightholder":"",
      "slugline":"lorem",
      "headline":"lorem",
      "version":"3",
      "description_html":"<p>Lorem ipsum abstract</p>",
      "located":"Prague",
      "pubstatus":"usable",
      "copyrightnotice":"",
      "body_html":"<p>Lorem ipsum body</p>",
      "usageterms":"",
      "priority":6,
      "genre":[
        {
          "code":"Article",
          "name":"Article (news)"
        }
      ],
      "versioncreated":"2018-01-18T09:31:58+0000",
      "firstpublished":"2018-01-18T09:31:58+0000",
      "charcount":16,
      "extra":{
        "custom-date":"2018-01-18T00:00:00+0000",
        "ID":"<p>custom botttom field text</p>",
        "limit-test":"<p>limit test field</p>"
      },
      "service":[
        {
          "code":"f",
          "name":"sports"
        }
      ],
      "readtime":0,
      "firstcreated":"2018-01-18T09:26:52+0000"
    }
    """
    Then the response status code should be 201

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/6/publish/" with body:
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
    Then the response status code should be 201

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/lorem"

    And the JSON node "seo_metadata._links.meta_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "seo_metadata._links.og_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "seo_metadata._links.twitter_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "seo_metadata.meta_title" should be equal to "This is my meta title"
    And the JSON node "seo_metadata.meta_description" should be equal to "This is my meta description"
    And the JSON node "seo_metadata.og_title" should be equal to "This is my og title"
    And the JSON node "seo_metadata.og_description" should be equal to "This is my og description"
    And the JSON node "seo_metadata.twitter_title" should be equal to "This is my twitter title"
    And the JSON node "seo_metadata.twitter_description" should be equal to "This is my twitter description"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "PATCH" request to "/api/v2/seo/2018-01-18T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc" with parameters:
      | key                  | value                                                           |
      | metaTitle            | This is my meta title edit                                      |
      | metaDescription      | This is my meta description edit                                |
      | ogTitle              | This is my og title edit                                        |
    Then the response status code should be 200
    And the JSON node "_links.meta_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "_links.og_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "_links.twitter_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "meta_title" should be equal to "This is my meta title edit"
    And the JSON node "meta_description" should be equal to "This is my meta description edit"
    And the JSON node "og_title" should be equal to "This is my og title edit"
    And the JSON node "og_description" should be equal to "This is my og description"
    And the JSON node "twitter_title" should be equal to "This is my twitter title"
    And the JSON node "twitter_description" should be equal to "This is my twitter description"

    And I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "POST" request to "/api/v2/packages/6/publish/" with body:
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
    Then the response status code should be 201

    Then I am authenticated as "test.user"
    And I add "Content-Type" header equal to "application/json"
    Then I send a "GET" request to "/api/v2/content/articles/lorem"
    Then the response status code should be 200
    And the JSON node "seo_metadata.meta_title" should be equal to "This is my meta title edit"
    And the JSON node "seo_metadata.meta_description" should be equal to "This is my meta description edit"
    And the JSON node "seo_metadata.og_title" should be equal to "This is my og title edit"
    And the JSON node "seo_metadata.og_description" should be equal to "This is my og description"
    And the JSON node "seo_metadata.twitter_title" should be equal to "This is my twitter title"
    And the JSON node "seo_metadata.twitter_description" should be equal to "This is my twitter description"
    And the JSON node "seo_metadata._links.meta_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "seo_metadata._links.og_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
    And the JSON node "seo_metadata._links.twitter_media_url.href" should be equal to "http://localhost/media/seo/0123456789abc.png"
