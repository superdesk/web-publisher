Content Lists
=============

What playlists are for music, content lists are for articles - customizable sets of content organized and ordered in a way that suits your output channel the best. For example, when you access some Publisher website homepage as visitor, chances are that the teasers for articles you see are curated by website editors using *Content lists*. The order of articles can be easily updated as news arrive, new articles added and some other removed etc.

Manual Content Lists
--------------------

With *manual content lists*, articles need to be dragg'n'dropped into the list manually. This gives complete freedom to editors which stories are put where. List can be (and should be) limited in length, so when new article is added to let's say top of it, last one drops out.

Automatic Content Lists
-----------------------

If list doesn't need to be fully customizable, or when it can be programmed with certain rules to collect appropirate articles, then *automatic content list* stepps in. 

For example, some block on the webiste shows most recent articles from section Sport, where metadata location is set to 'Europe' and author is not John Smith; in such situation, obviously editors don't need to put in manual labour, but rather use automated set of rules which add articles to the automatic list.

Built in criteria:

- ``route`` - an array of route ids, e.g. [1,5]

- ``author`` - an array of authors, e.g. ["Test Persona","Doe"]

- ``publishedBefore`` - date string, articles published before this date will be added to the list,
e.g. date: "2021-01-20". (date format must be YYYY-MM-DD)

- ``publishedAfter`` - date string, articles published after that date will be added to the list, format is the same as in the ``publishedBefore`` case.

- ``publishedAt`` - date string, when defined articles matching this publish dates will be added to the list when published, format is the same as in case of ``publishedBefore`` and ``publishedAfter``

- ``metadata`` - metadata is the dropdown field where filtering can be done by any of article's metadata: Categories, Genre, Urgency, Priority, Keywords, and any custom vocabulary that is set as a field in content profile in Superdesk

All criteria can be combined together which in the result it will add articles to the list (on publish) depending on your needs.
