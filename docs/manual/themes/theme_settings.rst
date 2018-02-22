Theme Settings
--------------

Settings of a theme are defined in ``theme.json`` configuration file which should be present in every theme directory.

An example of ``theme.json`` file with defined settings will look like:

.. code-block:: json

    {
        "name": "swp/default-theme",
        "title": "Default Theme",
        "description": "Superdesk Publisher default theme",
        "authors": [
            {
                "name": "Sourcefabric z.ú.",
                "email": "contact@sourcefabric.org",
                "homepage": "https://www.sourcefabric.org",
                "role": "Organization"
            }
        ],
        "settings": {
            "primary_font_family": {
                "label": "Primary Font Family",
                "value": "Roboto",
                "type": "string",
                "help": "The primary font",
                "options": [
                    {"value": "Roboto", "label": "Roboto"},
                    {"value": "Lato", "label": "Lato"},
                    {"value": "Oswald", "label": "Oswald"}
                ]
            },
            "secondary_font_family": {
                "value": "Roboto",
                "type": "string",
                "options": [
                    {"value": "Roboto", "label": "Roboto"},
                    {"value": "Lato", "label": "Lato"},
                    {"value": "Oswald", "label": "Oswald"}
                ]
            },
            "body_font_size": {
                "label": "Body Font Size",
                "value": 14,
                "type": "integer",
                "options": [
                    {"value": 14, "label": "14px"},
                    {"value": 16, "label": "16px"},
                    {"value": 18, "label": "18px"}
                ]
            }
        }
    }

In the ``settings`` property of the JSON file are defined the default theme's settings.

Each setting can be overridden by the API. See ``/settings/`` API endpoint for more details in the ``/api/doc`` route
in your Superdesk Publisher instance.

Read more about settings in :doc:`Settings </bundles/SWPSettingsBundle/settings_definitions>` chapter to find out more.

Every setting is a JSON object which can contain the following properties:

- ``label`` - Setting's label, will be visible in API when defined,
- ``value`` - Setting's value, will be visible in API when defined,
- ``type`` - Setting's type, either it's ``string``, ``integer``, ``boolean`` or ``array``.
- ``help`` - Settins's helper text.
- ``options`` - an array of optional values that can be used to implement select box.

Read more about theme's structure in :doc:`Themes </manual/themes/index>` chapter.

How to display current theme's settings in templates?
`````````````````````````````````````````````````````

.. code-block:: twig

    {# app/themes/<tenant_code>/<theme_name>/views/index.html.twig #}
    {{ themeSetting('primary_font_family') }} # will print "Roboto"

If the theme's setting doesn't exists an exception will be thrown with a proper message that it does not exist.


How to display current theme's settings using API?
``````````````````````````````````````````````````

Theme's settings can be accessed by calling an ``/theme/settings/`` API endpoint using ``GET`` method.

How to update current theme's settings using API?
`````````````````````````````````````````````````

To update theme's settings using API, a ``PATCH`` request must be submitted to the ``/settings/`` endpoint with the
JSON payload:

.. code-block:: twig

    {
        "settings": {
            "name": "primary_font_family",
            "value": "custom font"
        }
    }

How to restore current theme's settings using API?
``````````````````````````````````````````````````

There is a possibility to restore the current theme's settings to the default ones, defined in the ``theme.json`` file.

This can be done using API and calling a ``/settings/revert/{scope}`` endpint using ``POST`` method.
The ``scope`` parameter should be set to ``theme`` in order to restore settings for current theme.
