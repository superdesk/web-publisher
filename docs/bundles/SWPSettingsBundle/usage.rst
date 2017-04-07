Usage
=====

REST API
--------

List settings
`````````````

You can list all settings (with values loaded for scope) by API.

``GET /api/{version}/settings/``

Update settings
```````````````

You can update settings with API call:

.. code-block:: php

    curl -X "PATCH" -d "settings[name]=setting_name&settings[value]=setting_value" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/settings


In code
-------

Get all settings
````````````````

.. code-block:: php

    $settingsManager = $this->get('swp_settings.manager.settings');
    $settings = $settingsManager->all();

Get single setting
``````````````````

.. code-block:: php

    $settingsManager = $this->get('swp_settings.manager.settings');
    $setting = $settingsManager->get('setting_name');

Set setting value
`````````````````

.. code-block:: php

    $settingsManager = $this->get('swp_settings.manager.settings');
    $setting = $settingsManager->set('setting_name', 'setting value');

For details check ``SWP\Bundle\SettingsBundle\Manager\SettingsManager`` class.