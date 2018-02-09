Theme Logo
----------

Theme's logo can be uploaded by the API. Once it is uploaded, it can be rendered in theme's template.

How to upload custom theme's logo?
``````````````````````````````````

Theme's logo can be uploaded by making a ``POST`` call with the attached image to the API endpoint: ``/theme/logo_upload/``.
See ``/api/doc`` route in your Superdesk Publisher installation to find out more about API documentation.

This endpoint accepts ``jpg``, ``jpeg`` and ``png`` image extensions.

How to display path of the theme's logo using API?
``````````````````````````````````````````````````

Once the theme's logo has been uploaded using ``/theme/logo_upload/`` API endpoint, it is a time to display it.
The logo path is stored under the ``theme_logo`` setting name and can be accessed by calling ``/themes/settings/`` endpoint.

To get the image, just simply grab the ``theme_logo`` settings from the response and pass it as an argument to the url:
``/theme_logo/<theme_logo_setting_here>`` (e.g. ``/theme_logo/f2/e9/7c543ecad44807b0acab0b61e09a.png``), then an image will be streamed.

If ``theme_logo`` setting is not set, to get the default logo just use the ``/public/{fileName}.{fileExtension}`` API endpoint
which will help you get any image (logo in this case) from the ``public`` directory of your theme.

How to display path of the theme's logo in templates?
`````````````````````````````````````````````````````

There is a ``themeLogo`` twig function that accepts one argument. This argument is a path to the logo from ``public`` directory
in your theme in this example (Read more about it in :doc:`Work with theme assets </manual/themes/assets>` chapter).
If theme's logo is not uploaded, the function will fallback to the path provided in as an argument to that function.
Any path can be provided as an argument.

.. code-block:: twig

    {# app/themes/<tenant_code>/<theme_name>/views/index.html.twig #}
    <img src="{{ themeLogo(asset('theme/img/logo.png')) }}">
