@amp
@disable-fixtures
Feature: Ignore invalid embeds when converting to AMP
  In order to view the AMP version of the article with broken embeds
  As a Reader
  I want to be able to see an AMP article without the boken embeds.

  Scenario: Convert to AMP
    Given the following Tenants:
      | organization | name | code   | subdomain | domainName | enabled | default | code   | themeName      | ampEnabled  |
      | Default      | test | 123abc |           | localhost  | true    | true    | 123abc | swp/test-theme | true        |

    Given the following Routes:
      |  name | type       | slug |
      |  test | collection | test |

    Given the following Articles:
      | title               | route      | isPublishable     | status    | body |
      | First Test Article  | test       | true              | published |  <blockquote class="instagram-media" data-instgrm-captioned="" data-instgrm-version="7"> <div> <div> <div> </div> </div> <p><a href="https://www.instagram.com/p/BGnR5Tdnyys/" target="_blank">#warriors #dubnation #hometeam</a></p> <p>A photo posted by JEREMY MEEKS (@jmeeksofficial) on <time datetime="2016-06-13T23:51:22+00:00">Jun 13, 2016 at 4:51pm PDT</time></p> </div> <blockquote class="instagram-media" data-instgrm-captioned="" data-instgrm-version="7"> <p> </p> <div> <div> <div> </div> </div> <p><a href="https://www.instagram.com/p/BGp3nbXny7m/" target="_blank">Count down #housearrest #bayarea</a></p> <p>A photo posted by JEREMY MEEKS (@jmeeksofficial) on <time datetime="2016-06-14T23:59:27+00:00">Jun 14, 2016 at 4:59pm PDT</time></p> </div> </blockquote>    |

    When I go to "http://localhost/test/first-test-article?amp"
    Then the response status code should be 200
