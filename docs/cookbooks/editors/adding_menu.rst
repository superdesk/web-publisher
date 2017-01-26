Adding new menus
================

Adding new menu and its items is very easy. Once the menu is created the menu widget will be automatically created so you can use it instantly without a need to create it manually.

Newly created menu widget will, by default, use ``menu.html.twig`` template and will point to the menu you created.

Here is an example to demonstrate this:

Once the new menu with name ``Sports`` is created, menu widget will be automatically created with a name: ``Sports`` and template: ``menu.html.twig``. New menu widget, by default, will point to newly created menu (with name ``Sports``). When this menu widget will be used in template, the menu assigned to that widget will be automatically rendered i.e. the ``Sports`` menu.

.. note::

  Menu widget will be created automatically for you only for the main menus (root menus), not for the menu items.
