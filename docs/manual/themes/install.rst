Create or install a theme
-------------------------

To install theme assets you need to run ``swp:theme:install`` command.

.. code-block:: bash

    The swp:theme:install command installs your custom theme for given tenant:

        bin/console swp:theme:install <tenant> <theme_dir>

    You need specify the directory (theme_dir) argument to install
    theme from any directory:

        bin/console swp:theme:install <tenant> /dir/to/theme

    Once executed, it will create directory app/themes/<tenant>
    where <tenant> is the tenant code you typed in the first argument.

    To force an action, you need to add an option: --force:

        bin/console swp:theme:install <tenant> <theme_dir> --force

    To activate this theme in tenant, you need to add and option --activate:
        bin/console swp:theme:install <tenant> <theme_dir> --activate

    If option --processGeneratedData will be passed theme installator will
    generate declared in theme config elements like: routes, articles, menus and content lists
