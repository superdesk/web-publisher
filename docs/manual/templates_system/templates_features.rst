.. _templates_features:

Templates features
==================

.. include:: /components/TemplatesSystem/features/twig_nodes.rst


Stringy twig extensions
-----------------------

We have extended the twig syntax, adding a number of functions for working with strings from a php library. A list of the functions together with a description of each, and of how they are to be invoked in PHP can be found here: https://github.com/danielstjules/Stringy#instance-methods

To call one of these functions in twig, if it returns a boolean, it is available as a twig function. So, for example, the function contains() can be called like this in twig:

.. code-block:: twig

    {% set string_var = 'contains' %}
    {% if contains(string_var, 'tain') %}string_var{% endif %} // will render contains

Any php function which returns a string is available in twig as a filter. So, for example, the function between() can be called like this in twig:

.. code-block:: twig

    {% set string_var = 'Beginning' %}
    {{ string_var|between('Be', 'ning') }} // will render gin

And the function camelize(), which doesn't require any parameters, can simply be called like this:

.. code-block:: twig

    {% set string_var = 'Beginning' %}
    {{ string_var|camelize }} // will render bEGINNING
