Scope context
=============

.. hint::

    ``Scope`` defines level for custom changes. If setting have scope ``user`` then every user will have his own value for this setting.

``ScopeContext`` class defines available scopes (in ``getScopes()`` method). Every setting must have scope and can have
setting owner. Scope Context collects owners for defined scopes from current application state.

.. code-block:: yaml

    ...
    $scopeContext = new \SWP\Bundle\SettingsBundle\Context\ScopeContext();
    ...

    // Set user in scope
    $scopeContext->setScopeOwner(ScopeContextInterface::SCOPE_USER, $user);

.. note::

    Owner object set to scope context must implement ``SettingsOwnerInterface``. Scope owner allows for system to fill
    settings with correct custom set values kept in storage.

Bundle already register event subscriber responsible for setting currently logged user in scope context -
``SWP\Bundle\SettingsBundle\EventSubscriber\ScopeContextSubscriber``.