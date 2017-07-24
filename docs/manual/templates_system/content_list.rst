Handling Content List Items
===========================

Listing Content List Items
--------------------------

.. note::

    Content List can store many different content types (articles, events, packages).

Content List
````````````

Usage:

.. code-block:: twig

    <ul>
    {% gimme contentList with { contentListName: "List1" } %}
        <li>{{ contentList.name }}</li> <!-- Content list name -->
        <li>{{ contentList.description }}</li> <!-- Content list description -->
        <li>{{ contentList.type }}</li> <!-- Content list type) -->
    {% endgimme %}
    </ul>

Parameters:

.. code-block:: twig

    {% gimme contentList with { contentListId: 1 } %} - select list by it's id.


.. code-block:: twig

    {% gimme contentList with { contentListName: "List Name" } %} - select list by it's name.


Content List Items
``````````````````

Usage:

.. code-block:: twig

    <ul>
    {% gimmelist item from contentListItems with { contentListName: "List1" } %}
        <li>{{ item.content.title }}</li> <!-- Article title -->
        <li>{{ item.position }}</li> <!-- Item position in list -->
        <li>{{ item.sticky ? "pinned" : "not pinned" }}</li> <!-- Checks if item is sticky (positioned on top of list) -->
    {% endgimmelist %}
    </ul>

Parameters:

.. code-block:: twig

    {% gimmelist item from contentListItems with { contentListId: 1 } %} - select list by it's Id.


.. code-block:: twig

    {% gimmelist item from contentListItems with { contentListId: 1, sticky: true } %} - filter by sticky value.