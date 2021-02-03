Content Lists
=============

What playlists are to music, content lists are to articles - customizable sets of content organized and ordered in the way that suits your output channel best. For example, when you access a Publisher website homepage as a visitor, chances are that the teasers for articles you see are curated by website editors using *Content lists*. The order of articles can be easily updated as news arrives, new articles are added, some others removed etc.

Manual Content Lists
--------------------

With *manual content lists*, articles need to be drag-and-dropped into the list manually. This gives complete freedom to editors to put which stories where. A list can be (and should be) limited in length, so when a new article is added on top, the last one drops out.

Automatic Content Lists
-----------------------

If a list doesn't need to be fully customizable, or if it can be programmed with certain rules to collect appropriate articles, then the *automatic content list* steps in. 

For example, a block on the webiste shows the most recent articles from the Sport section, where the metadata location is set to 'Europe' and author is not John Smith; in this situation, obviously editors don't need to put in manual labour, but can rather use an automated set of rules to add articles to the automatic list.

Built in criteria:

- ``route`` - an array of route ids, e.g. [1,5]

- ``author`` - an array of authors, e.g. ["Test Persona","Doe"]

- ``publishedBefore`` - date string, articles published before this date will be added to the list,
e.g. date: "2021-01-20". (date format must be YYYY-MM-DD)

- ``publishedAfter`` - date string, articles published after that date will be added to the list, format is the same as in the ``publishedBefore`` case.

- ``publishedAt`` - date string, when defined articles matching this publish date are added to the list when published, the format is the same as in case of ``publishedBefore`` and ``publishedAfter``

- ``metadata`` - metadata is the dropdown field where filtering can be done by any of an article's metadata: Categories, Genre, Urgency, Priority, Keywords, and any custom vocabulary that is set as a field in a content profile in Superdesk

All criteria can be combined together with that the result it will add articles to the list (on publish) depending on your needs.
