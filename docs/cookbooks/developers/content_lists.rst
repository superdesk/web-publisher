Content Lists
=============

Automatic Content Lists
~~~~~~~~~~~~~~~~~~~~~~~

Pinned articles
---------------

This feature gives you a way of exposing more important articles on top of the list.

In automatic content lists you have the possibility to pin/unpin articles from the list. If you decide to pin one of the article from the list, it will always show up on top of the list, no matter how many new articles will be added to that list.

You can pin as many articles as you want.

Pinned article can be unpinned too. Once it's done, the unpinned article will be removed from the top of the list and will remain in the list on the corresponding position.

By default, articles in Automatic Content List are ordered by "sticky" flag (desc)
 and item creation date (desc).

How articles are being added to Automatic Content Lists
-------------------------------------------------------

When a new list is created and relevant criteria/filters are set for that list,
articles to be published will automatically be added to the list but only when they meet the list's criteria.

If the list's criteria are not defined, or if the articles do not match the list's criteria, articles will not be added to the list.

Here is an example to demonstrate this:

Given that your list's criteria/filters are set to match "Sports" route so when you publish an article which is assigned to "Sports" route, it will be automatically assigned to the list.

Built in criteria:

- ``route`` - an array of route ids, e.g. [1,5]

- ``author`` - an array of authors, e.g. ["Test Persona","Doe"]

- ``publishedBefore`` - date string, articles published before this date will be added to the list, e.g. date: "2017-01-20". (date format must be YYYY-MM-DD)

- ``publishedAfter`` - date string, articles published after that date will be added to the list, format is the same as in the ``publishedBefore`` case.

- ``publishedAt`` - date string, when defined articles matching this publish dates will be added to the list when published, format is the same as in case of ``publishedBefore`` and ``publishedAfter``

- ``metadata`` - metadata field is json string, e.g. ``{"metadata":{"language":"en"}}``. It matches article's metadata, and you can use all metadata fields that are defined for the article, i.e.: language, located etc.

``metadata`` allows also filtering based on expression. It uses DataFilter class (SWP\Bundle\CoreBundle\Filter\DataFilter) methods for metadata checking.

Example usage: ``{"metadata": "filter.contains('subject').containsItem('code', '001').containsItem('code', '123')"}``

Possible methods are:

- ``contains`` - checks if metadata have provided key and sets is as a base for next methods calls.

- ``containsItem`` - checks if current base is an array and looks for provided key and value match


All criteria can be combined together which in the result it will add articles to the list (on publish) depending on your needs.


