Configuration Reference
=======================

The SWPContentListBundle can be configured under the ``swp_content_list`` key in your configuration file.
This section describes the whole bundle's configuration.

Full Default Configuration
--------------------------

.. code-block:: yaml

        # app/config/config.yml
        swp_content_list:
            persistence:
                orm:
                    enabled: true
                    classes:
                        content_list:
                            model: SWP\Component\ContentList\Model\ContentList
                            repository: SWP\Bundle\ContentListBundle\Doctrine\ORM\ContentListRepository
                            factory: SWP\Bundle\StorageBundle\Factory\Factory
                            object_manager_name: ~
                        content_list_item:
                            model: SWP\Component\ContentList\Model\ContentListItem
                            repository: SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository
                            factory: SWP\Bundle\StorageBundle\Factory\Factory
                            object_manager_name: ~

persistence
~~~~~~~~~~~

``persistence``
...............

``orm``
"""""""

    .. code-block:: yaml

        # app/config/config.yml
        swp_content_list:
            # ..
            persistence:
                orm:
                    enabled: true

``enabled``
***********

**type**: ``boolean`` **default**: ``false``

If ``true``, ORM is enabled in the service container.

ORM can be enabled by multiple ways such as:

    .. code-block:: yaml

        orm: ~ # use default configuration
        # or
        orm: true # straight way
        # or
        orm:
            enabled: true ... # or any other option under 'orm'

``classes``
***********

.. code-block:: yaml

        # app/config/config.yml
        swp_content_list:
            # ..
            persistence:
                orm:
                    # ..
                    classes:
                        content_list:
                            model: SWP\Component\ContentList\Model\ContentList
                            repository: SWP\Bundle\ContentListBundle\Doctrine\ORM\ContentListRepository
                            factory: SWP\Bundle\StorageBundle\Factory\Factory
                            object_manager_name: ~
                        content_list_item:
                            model: SWP\Component\ContentList\Model\ContentListItem
                            repository: SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository
                            factory: SWP\Bundle\StorageBundle\Factory\Factory
                            object_manager_name: ~

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If Doctrine ORM persistence backend is enabled it will register ``swp.object_manager.content_list`` service
which is an alias for ``doctrine.orm.default_entity_manager``.

``content_list.model``
**********************

**type**: ``string`` **default**: ``SWP\Component\ContentList\Model\ContentList``

The FQCN of the ContentList model class which is of type ``SWP\Component\ContentList\Model\ContentListInterface``.

``content_list.factory``
************************

**type**: ``string`` **default**: ``SWP\Bundle\StorageBundle\Factory\Factory``

The FQCN of the ContentList Factory class.

``content_list.repository``
***************************

**type**: ``string`` **default**: ``SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository``

The FQCN of the ContentList Repository class.

``content_list.object_manager_name``
************************************

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If Doctrine ORM persistence backend is enabled it will register ``swp.object_manager.content_list`` service
which is an alias for ``doctrine.orm.default_entity_manager``.

``content_list_item.model``
***************************

**type**: ``string`` **default**: ``SWP\Component\ContentList\Model\ContentListItem``

The FQCN of the ContentListItem model class which is of type ``SWP\Component\ContentList\Model\ContentListItemInterface``.

``content_list_item.factory``
*****************************

**type**: ``string`` **default**: ``SWP\Bundle\StorageBundle\Factory\Factory``

The FQCN of the ContentListItem Factory class.

``content_list_item.repository``
********************************

**type**: ``string`` **default**: ``SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository``

The FQCN of the ContentListItem Repository class.

``content_list_item.object_manager_name``
*****************************************

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If Doctrine ORM persistence backend is enabled it will register ``swp.object_manager.content_list_item`` service
which is an alias for ``doctrine.orm.default_entity_manager``.
