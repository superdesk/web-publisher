Configuration Reference
=======================

The SWPRuleBundle can be configured under the ``swp_rule`` key in your configuration file.
This section describes the whole bundle's configuration.

Full Default Configuration
--------------------------

.. code-block:: yaml

        # app/config/config.yml
        swp_rule:
            persistence:
                orm:
                    enabled: true
                    classes:
                        rule:
                            model: SWP\Component\Rule\Model\Rule
                            repository: SWP\Bundle\RuleBundle\Doctrine\ORM\RuleRepository
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
        swp_rule:
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
        swp_rule:
            # ..
            persistence:
                orm:
                    # ..
                    classes:
                        rule:
                            model: SWP\Component\Rule\Model\Rule
                            repository: SWP\Bundle\RuleBundle\Doctrine\ORM\RuleRepository
                            factory: SWP\Bundle\StorageBundle\Factory\Factory
                            object_manager_name: ~

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If Doctrine ORM persistence backend is enabled it will register ``swp.object_manager.rule`` service
which is an alias for ``doctrine.orm.default_entity_manager``.

``rule.model``
**************

**type**: ``string`` **default**: ``SWP\Component\Rule\Model\Rule``

The FQCN of the Rule model class which is of type ``SWP\Component\Rule\Model\RuleInterface``.

``rule.factory``
****************

**type**: ``string`` **default**: ``SWP\Bundle\StorageBundle\Factory\Factory``

The FQCN of the Rule Factory class.

``rule.repository``
*******************

**type**: ``string`` **default**: ``SWP\Bundle\RuleBundle\Doctrine\ORM\RuleRepository``

The FQCN of the Rule Repository class.

``rule.object_manager_name``
****************************

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If Doctrine ORM persistence backend is enabled it will register ``swp.object_manager.rule`` service
which is an alias for ``doctrine.orm.default_entity_manager``.
