Creating custom templates
=========================

Setting error pages from theme
------------------------------

Publisher provides simple default templates for error pages. You can find them in :code:`app/Resources/TwigBundle/views/Exception/` directory.

To override these templates from theme you need to create :code:`TwigBundle/views/Exception/` directory in your theme, and put there new error pages files.

Example Structure:

.. code-block:: bash

    ThemeName/
    └─ TwigBundle/
       └─ views/
          └─ Exception/
             ├─ error404.html.twig
             ├─ error403.html.twig
             ├─ error500.html.twig
             ├─ error.html.twig      # All other HTML errors

Testing error pages during theme development
--------------------------------------------

You can use URLs like

.. code-block:: bash

    http://wepublisher.dev/app_dev.php/_error/404
    http://wepublisher.dev/app_dev.php/_error/403
    http://wepublisher.dev/app_dev.php/_error/500
    http://wepublisher.dev/app_dev.php/_error/501 # error.html.twig will be loaded

to preview the error page for a given status code as HTML.
