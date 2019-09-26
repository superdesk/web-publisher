@disable-fixtures
Feature: Working with Slideshow and Slideshow Items Loader
  In order to work with content list items
  As a template implementor
  I want to be able to list slideshow and it's items

  Scenario: Rendering slideshow's items
    Given the following Tenants:
      | organization | name | subdomain | domain_name | enabled | default |
      | Default      | test |           | localhost   | true    | true    |

    Given the following Articles:
      | title               | route      | status    |
      | First Test Article  | Test Route | published |

    Given the following Article Media:
      | body           | headline  | article                 | key   | mimetype  |
      | media1         | media1    | First Test Article      | item1 | image/png |

    Given the following Article Media:
      | body           | headline  | article                 | key   | mimetype  |
      | media2         | media2    | First Test Article      | item2 | image/png |

    Given the following Slideshows:
      | code                | article               |
      | slideshow           | First Test Article    |


    Given the following Slideshow Items:
      | slideshow         | media    |
      | slideshow         | item2    |
      | slideshow         | item1    |

    And I set "first-test-article" as a current article in the context

    And I render a template with content:
"""
{% gimmelist slideshow from slideshows with { article: gimme.article } %}
{{ slideshow.code }}
{% gimmelist slideshowItem from slideshowItems with { article: gimme.article, slideshow: slideshow } %}
{{slideshowItem.articleMedia.key}}
{% endgimmelist %}
{% endgimmelist %}
"""
    Then rendered template should be equal to:
    """
slideshow
item2
item1

    """
