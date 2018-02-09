Theme Logo
----------

Theme's logo can be uploaded by the API. Once it is uploaded, it can be rendered in theme's template.

How to display path of the theme's logo in templates?
`````````````````````````````````````````````````````

There is a ``themeLogo`` twig function that accepts one argument. This argument is a path to the logo from ``public`` directory
in your theme in this example. If theme's logo is not uploaded, the function will fallback to the
path provided in as an argument to that function. Any path can be provided as an argument.

.. code-block:: twig

    {# app/themes/<tenant_code>/<theme_name>/views/index.html.twig #}
    <img src="{{ themeLogo(asset('theme/img/logo.png')) }}">
