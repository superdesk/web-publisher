Settings definitions
====================

In your application or bundle configuration add settings definitions:

.. code-block:: yaml

    swp_settings:
        settings:
            registration_confirmation.template:
                value: "example value"
                scope: user
                type: string

Minimal definition looks like that:

.. code-block:: yaml

    swp_settings:
        settings:
            registration_confirmation.template: ~

.. note::

    Default values:
        * value: ``null``
        * scope: ``global`` (possible options: ``global``, ``user``)
        * type: ``string`` (possible options: ``string``, ``array``)


Settings scopes
```````````````

Scope defines level for custom changes. If setting have scope ``user`` then every user will have his own value for this setting.

Settings value types
````````````````````

Setting value can be ``string`` or ``array`` (it will be saved as json).

Example with array as value:

.. code-block:: yaml

    parameters:
        array_value:
            a: 1
            b: 2

    swp_settings:
        settings:
            custom_setting_1:
                value: "%array_value%"
            custom_setting_2:
                value: '{"a":1, "b": 2}'