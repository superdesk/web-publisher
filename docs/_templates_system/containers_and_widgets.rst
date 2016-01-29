Containers and widgets concept
==============================

Template file can have (this is not required feature) multiple or one containers.
Every container can have default value witch can be override by attached to container widgets.
This same container can be placed in many templates or even many times on this same template.

What is Container
`````````````````

Containers are the way to give move control over template for editors. Good implemented can transform one theme into many various end results.
Every container can have default parameters and content, can be also hidden when not needed. Container twig tag keep html syntax always up to date with javascript front live management expectations.

What is Widget
``````````````

Widget can replace default container value. Many widgets can be attached to container, they can be also reordered. Can be many types of widget witch can represent different features.

Widget examples:

 * Newsletter signup form
 * Facebook components like page widget or comments widget
 * Simple HTML widget wit your own custom html rendered by widget
 * Airtime player widget
 * etc.

How to build flexible templates
```````````````````````````````

Most important is to use containers as your `block` elements in template, and allow for users to override specific places - ex. article sidebar content, fotter, front page content blocks.

Are widgets and containers works with cache?
````````````````````````````````````````````

Yes, they work good with all caching systems inside WebPublisher.
