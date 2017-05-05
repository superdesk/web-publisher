Updating database schema after customizing model's mapping
==========================================================

If you modified one of the model's mapping file or you added completely new model with mapping, you will need to update the database schema.

There are two ways to do it.

* via direct database schema update:

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

* via migrations:

We recommend to update the schema using migrations so you can easily rollback and/or deploy new changes to the database without any issues.

.. code-block:: bash

    $ php app/console doctrine:migrations:diff
    $ php app/console doctrine:migrations:migrate

.. tip::

    Read more about the database modifications and migrations in the `Symfony documentation here <http://symfony.com/doc/current/book/doctrine.html#creating-the-database-tables-schema>`_.
