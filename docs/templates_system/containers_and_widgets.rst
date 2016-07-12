Containers and widgets concept
==============================

Containers are intended to give editors more control over templates. Properly implemented, they can transform a theme.

What is a Container?
````````````````````

A template file can optionally have one or more containers, `block` elements that can be overriden in specific places.
For example, article sidebar content, footer, or front page content blocks.
This same container can be placed in many different templates, or even many times in the same template.

Every container can have default parameters and content, and can be hidden when not needed.
The container twig tag keeps HTML syntax always up to date with JavaScript live management expectations.
Container default values can be overridden by widgets.

What is a Widget?
`````````````````

Widgets can be attached to a container in any order. Many types of widget can represent different features, for example:

 * Newsletter signup form
 * Facebook components, like a page widget or comments widget
 * Simple HTML widget with your own custom HTML rendered by widget
 * Airtime player widget

How to create a new type of widget?
```````````````````````````````````

To create a new type of widget, you create a new class in ``/src/SWP/Component/TemplatesSystem/Gimme/Widget which extends``
the AbstractWidgetHandler.php in that same folder.
As well as having to implement the render function, you can define what parameters you're expecting in the widget model
by adding a static variable to your class called ``$expectedParameters``. For example:

.. code-block:: php

     protected static $expectedParameters = array(
        'parameter_name' => [
            'type' => 'string',               // or bool, int, float
            'default' => 'default_value'      // if no default is provided, the parameter must be set
        ]
    );

Do widgets and containers work with caches?
```````````````````````````````````````````

Yes, they are designed to work well with all caching systems used by Web Publisher.
