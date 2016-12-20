Models
======

ContentList
-----------

Every content list is represented by a **ContentList** model which by default has the following properties:

+--------------+-------------------------------------------+
| Method       | Description                               |
+==============+===========================================+
| id           | Unique identifier                         |
+--------------+-------------------------------------------+
| description  | List's description                        |
+--------------+-------------------------------------------+
| name         | List's name                               |
+--------------+-------------------------------------------+
| type         | List's type (``automatic`` or ``manual``) |
+--------------+-------------------------------------------+
| cacheLifeTime| List cache life time in seconds           |
+--------------+-------------------------------------------+
| limit        | List limit                                |
+--------------+-------------------------------------------+
| items        | Collection of list items                  |
+--------------+-------------------------------------------+
| enabled      | Indicates whether the list is enabled     |
+--------------+-------------------------------------------+
| createdAt    | Date of creation                          |
+--------------+-------------------------------------------+
| updatedAt    | Date of last update                       |
+--------------+-------------------------------------------+
| deletedAt    | Indicates whether the list is deleted     |
+--------------+-------------------------------------------+

.. note::

    This model implements ``SWP\Component\ContentList\Model\ContentListInterface``.

ContentListItem
---------------

Every content list item is represented by a **ContentListItem** model which by default has the following properties:

+--------------+-------------------------------------------+
| Method       | Description                               |
+==============+===========================================+
| id           | Unique identifier                         |
+--------------+-------------------------------------------+
| position     | List item position                        |
+--------------+-------------------------------------------+
| content      | Object of type ``ListContentInterface``   |
+--------------+-------------------------------------------+
| enabled      | Indicates whether the item is enabled     |
+--------------+-------------------------------------------+
| sticky       | Defines whether content is sticky or not  |
+--------------+-------------------------------------------+
| createdAt    | Date of creation                          |
+--------------+-------------------------------------------+
| updatedAt    | Date of last update                       |
+--------------+-------------------------------------------+
| deletedAt    | Indicates whether the item is deleted     |
+--------------+-------------------------------------------+

.. note::

    Read more about :ref:`bundle_content_list_list_content-interface`.

.. note::

    This model implements ``SWP\Component\ContentList\Model\ContentListItemInterface``.

Repository Interface
====================

This component contains ``SWP\Component\ContentList\Repository\ContentListRepositoryInterface`` interface
which can be used by your custom entity repository, in order to make it working with :doc:`ContentListBundle </bundles/SWPContentListBundle/index>`.
