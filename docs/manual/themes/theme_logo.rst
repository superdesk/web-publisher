Theme Logo
----------

Theme's logo can be uploaded by the API. Once it is uploaded, it can be rendered in theme's template.

There is possibility to upload up to three different logos per theme.

How to upload custom theme's logo?
``````````````````````````````````

The main theme's logo can be uploaded by making a ``POST`` call with the attached image to the API endpoint: ``/theme/logo_upload/``.

To upload second and third logo, a ``POST``request to ``/theme/logo_upload/<theme_setting_name>`` must be done.

Where ``<theme_setting_name>`` should be replaced by ``theme_logo_second`` for second logo and ``theme_logo_third`` for the third logo.

See ``/api/doc`` route in your Superdesk Publisher installation to find out more about API documentation.

This endpoint accepts ``jpg``, ``jpeg`` and ``png`` image extensions.

How to display path of the theme's logo using API?
``````````````````````````````````````````````````

Once the theme logos are uploaded using ``/theme/logo_upload/`` API endpoint, it is a time to display them.
The logos paths are stored under the ``theme_logo`` (main logo), ``theme_logo_second`` and ``theme_logo_third`` settings name and can be accessed by calling ``/themes/settings/`` endpoint.

To get the image, just simply grab the ``theme_logo`` or ``theme_logo_second`` or ``theme_logo_third``  setting from the response and pass its value as an argument to the url:
``/theme_logo/<theme_logo_setting_here>`` (e.g. ``/theme_logo/f2/e9/7c543ecad44807b0acab0b61e09a.png``), then an image will be streamed.

If the value of ``theme_logo``, ``theme_logo_second`` or ``theme_logo_third`` setting is not set, to get the default logo just use the ``/public/{fileName}.{fileExtension}`` API endpoint
which will help you get any image (logo in this case) from the ``public`` directory of your theme.

How to display path of the theme's logo in templates?
`````````````````````````````````````````````````````

There is a ``themeLogo`` twig function which accepts two arguments. These arguments are: a path to the logo from ``public`` directory
in your theme in this example (Read more about it in :doc:`Work with theme assets </manual/themes/assets>` chapter).
If theme's logo is not uploaded, the function will fallback to the path provided in as a first argument to that function.

The second argument of that function is theme setting name, e.g. ``theme_logo_second``. If not passed the default logo is loaded from
``theme_logo`` setting.

Any path can be provided as an argument.

.. code-block:: twig

    {# app/themes/<tenant_code>/<theme_name>/views/index.html.twig #}
    <img src="{{ themeLogo(asset('theme/img/logo.png')) }}">
    <img src="{{ themeLogo(asset('theme/img/logo.png'), 'theme_logo_second') }}">
