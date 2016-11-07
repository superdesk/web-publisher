Models
======

ContentList
-----------

ContentList model is a main class which defines default list properties.
This includes list's items, name, description and more:

- list can be limited to display certain number of items
- list can have cache life time defined in seconds (which is useful, for example, if you want to cache list for some time when rendering it on frontend)
- list can be of one of the types:
    - automatic
    - manual

Automatic list is meant to be created manually but the items in that list should not be draggable and droppable.
It just a flat list that you can add items and simply render list with it's items. Whatever content you want to place in this list you should be able to do it. An example can be that if some part of your business logic is able to
decide where the article should go, if it matches some criteria, you can use that logic and add an article to the list automatically - this list will be then called automatic list.

As in the case of Automatic lists, the Manual list is meant to be created manually but you should be able to
add, remove, drag and drop, sort items in this list manually by simply linking items to lists.

ContentListItem
---------------

ContentListItem model represents an item which can be placed inside content list.
It has a reference to content list, position at which it should be placed inside the list and a content.
Content can be of any type, but it should implement ``SWP\Component\ContentList\Model\ListContentInterface``.

.. _bundle_content_list_list_content-interface:

ListContentInterface
--------------------

This interface should be implemented by your class if you want objects of that type to be a content of the list.
For example, if you have ``Article`` class in your project and you want to make objects of this type to be a content of list item, it needs to implement ``SWP\Component\ContentList\Model\ListContentInterface``.
