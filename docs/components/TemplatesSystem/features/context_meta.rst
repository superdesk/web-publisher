Context and Meta objects
========================

Why Meta objects?
-----------------

Meta objects provides extra layer between your internal documents/entities and this what is available for theme developer (Templator).
Thanks to this feature you can make more changes in Your project code and data structures without breaking templates.

How to create Meta?
-------------------

Every Meta object requires :code:`Context`, :code:`Value` and :code:`Configuration`.

* Context - it's special service class used for collecting all meta's and resolving meta objects inside other meta's
* Value - object or array with data
* Configuration - array with configuration definitions for provided object.

Create  :code:`Meta` manually:

.. code-block:: php

    <?php

    ...
    use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

    return new Meta($context, $value, $configuration);

Create  :code:`Meta` with  :code:`MetaFactory`:

.. code-block:: php

    <?php

    ...
    use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
    use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

    $metaFactory = new MetaFactory($context);

    return $metaFactory->create($value, $configuration);


What is Context?
----------------

:code:`Context` is a special service class used for collecting all meta's and resolving meta objects inside other meta's.
It can collect all available configurations for meta's, and convert provided objects into meta's when there is configuration for it.

.. note::

    When property of :code:`Meta` object can be itself a :code:`Meta` instance (there is configuration for it) Context will automatically process it.

Example yaml configuration file for object (context can read config from :code:`.yml` files).

.. code-block:: yaml

    name: article
    class: "SWP\\Component\\TemplatesSystem\\Tests\\Article"
    description: Article Meta is representation of Article in Superdesk Web Publisher.
    properties:
        title:
            description: "Article title, max 160 characters, can't have any html tags"
            type: text
        keywords:
            description: "Article keywords"
            type: text
    to_string: title

.. note::

    Configurations are used to manually expose properties from provided data, and create documentation for templators.

All objects passed to template should be Meta's.