Navigation management
=====================

**Navigations** are menus that you can use on your websites. Advantage of creating them here (and not building navigation menu only in theme) is that it can later be managed in Superdesk Publisher interface (meaning menu items added, removed and reordered) even by website editors, and not theme developers. 

.. image:: navigation-management-01.png
   :alt: Navigation management
   :align: center

Only after configuring *Routes*, we can proceed to configure *Navigation*. That's because navigation is built of **menu items**, and menu items are partly defined by routes. 

.. image:: navigation-management-02.png
   :alt: Menu item type
   :align: center

However, it doesn't have to be the case, as menu items can also be custom uris. But as SEO algorythms sort-of penalize navigations with external links, it should be used wisely (ie. to make a link to specific custom route).

.. image:: navigation-management-03.png
   :alt: Navigation management
   :align: center

All elements of **menu item** definition are

- Name
- Label - value that is shown on frontend for that menu item in navigation menu
- Parent - useful when building nested, drop-down menus
- Route - one of previously defined routes
- Uri - automatically filled in when route is selected
