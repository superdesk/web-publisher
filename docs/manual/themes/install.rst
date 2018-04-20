Create or install a theme
-------------------------

To install theme assets you need to run ``swp:theme:install`` command.

.. code-block:: bash

    The swp:theme:install command installs your custom theme for given tenant:

        app/console swp:theme:install <tenant> <theme_dir>

    You need specify the directory (theme_dir) argument to install
    theme from any directory:

        app/console swp:theme:install <tenant> /dir/to/theme

    Once executed, it will create directory app/themes/<tenant>
    where <tenant> is the tenant code you typed in the first argument.

    To force an action, you need to add an option: --force:

        app/console swp:theme:install <tenant> <theme_dir> --force

    To activate this theme in tenant, you need to add and option --activate:
        app/console swp:theme:install <tenant> <theme_dir> --activate

    If option --processGeneratedData will be passed theme installator will
    generate declared in theme config elements like: routes, articles, menus, widgets,
    content lists and containers
