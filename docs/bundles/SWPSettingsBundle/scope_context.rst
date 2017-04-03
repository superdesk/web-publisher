Scope context
=============

``ScopeContext`` class defines available scopes (in ``getScopes()`` method). Every setting must have scope and can have
setting owner. Scope Context collects owners for defined scopes from current application state.

.. code-block:: yaml

    // Set user in scope
    $this->scopeContext->setScopeOwner(ScopeContextInterface::SCOPE_USER, $user);

.. note::

    Object set to scope context must implement ``SettingsOwnerInterface``

Bundle already register event subscriber responsible for setting currently logged user in scope context -
``SWP\Bundle\SettingsBundle\EventSubscriber\ScopeContextSubscriber``.