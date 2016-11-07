Widgets
=======

A container may have n widgets, each of which represents a fragment of the part of the page which is represented by the container. See :doc:`/templates_system/containers_and_widgets` for more details.

How to create a new type of widget?
```````````````````````````````````
Add a new constant to the /src/SWP/Bundle/TemplatesSystemBundle/Model/WidgetModel class, and a reference to a class which extends ``AbstractWidgetHandler.php`` in ``/src/SWP/Component/TemplatesSystem/Gimme/Widget``

For example:

.. code-block:: php

  class WidgetModel implements WidgetModelInterface, TenantAwareInterface, TimestampableInterface
  {
      const TYPE_NEW = 1;
      ...

      protected $types = [
          self::TYPE_NEW => '\\SWP\\Component\\TemplatesSystem\\Gimme\\Widget\\NewWidgetHandler',
          ...
      ];

      ...

You must then create that class which you have referenced.

As well as having to implement the render function in this class, you can define what, if any parameters should be set in an instance of a widget model which references this class
by adding a static variable called ``$expectedParameters``.

For example:

.. code-block:: php

  class NewWidgetHandler extends AbstractWidgetHandler
  {
    protected static $expectedParameters = array(
        'parameter_name' => [
            'type' => 'string',            // or bool, int, float
            'default' => 'default_value'   // if no default is provided, the parameter must be set
        ]
    );

    ...

If there is no default value, the default value, by default, will be null.

Typically, the render function will use the template engine to render a template which requires the given parameters.

For example:

.. code-block:: php

  class NewWidgetHandler extends AbstractWidgetHandler
  {
    ...

    /**
     * Render widget content.
     *
     * @return string
     */
    public function render()
    {
        return $this->renderTemplate('template_name.html.twig');
    }

    ...

The referenced twig file should be located in the theme's views/widgets directory.
The implementation of the renderTemplate method in the base class prepends 'widgets/' to the given string, and passes this argument, along with the expectedParameters and their values in an associative array, to the template engine's render method.
