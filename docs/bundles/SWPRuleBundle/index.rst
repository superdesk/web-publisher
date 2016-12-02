RuleBundle
==========

This bundle provides a simple business rules engine for Symfony applications.

:doc:`/components/Rule/index`, which is used by this bundle, provides a generic interface to create different type of rule applicators, models etc., which in turn help to create powerful business rules engine.

It means you can create your own rules and apply them to whatever objects you need:

.. code-block:: text

    # Get the special price if
    user.getGroup() in ['good_customers', 'collaborator']

    # Promote article to the homepage when
    article.commentCount > 100 and article.category not in ["misc"]

    # Send an alert when
    product.stock < 15


.. toctree::
    :numbered:
    :maxdepth: 2

    prerequisites
    installation
    usage
    models
    configuration
