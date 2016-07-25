Menus
=====

For the creation and rendering of menus, we are using the `SymfonyCMFMenuBundle`_ which extends the `KNPMenuBundle`_

To add a menu to a container, you should first create a Widget of type menu, setting its menu_name parameter.

Then using the swp_api_templates_create_menu route, you create a menu with the same name.

Then using the swp_api_templates_create_menu_node route, you create the nodes of this menu. The same route can be used to create nodes beneath nodes.

So to create a menu called main, with a node called home, and sub node called kitchen, and a sub node of this sub node called table you would POST the required data to the following routes:
::
  /api/{version}/templates/menus
  /api/{version}/templates/menunodes/main/
  /api/{version}/templates/menunodes/main/home
  /api/{version}/templates/menunodes/main/home/kitchen


The data requirements are defined in the TemplateEngineBundle in Form/Type/MenuType.php and Form/Type/MenuNodeType.php
The menus and their nodes can be managed using the part of the api defined in the same bundle, in Controller/MenuController.php and Controller/MenuNodeController.php

.. _SymfonyCMFMenuBundle: https://github.com/symfony-cmf/menu-bundle
.. _KNPMenuBundle: https://github.com/KnpLabs/KnpMenuBundle

