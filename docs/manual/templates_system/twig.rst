Rendering pages with Twig
=========================

Superdesk Publisher uses `Twig templating engine`_ to render website HTML. **Twig** is modern, flexible, extensible and secure templating system, and has great documentation_, as well as active support community at `Stack Overflow`_.

.. _Twig templating engine: http://twig.sensiolabs.org
.. _documentation: http://twig.sensiolabs.org/documentation
.. _Stack Overflow: https://stackoverflow.com/

This is how Twig code looks like:

.. code-block:: twig

    {% for user in users %}
    * {{ user.name }}
    {% else %}
       No users have been found.
    {% endfor %}

If you are creating completely new theme for your Publisher project, or going to modify some of the existing demo themes, you can follow :doc:`this handy guide </manual/getting_started/setting-up>`.

Generally, if starting from scratch, we advise you to develop your HTML/CSS/JS first with some dummy content, and once it's ready, you can proceed with translating this markup into twig templates.

We have developed three demo themes which can serve as a refference for quick start (more about it :doc:`here </manual/themes/example_themes>`)

- *Superdesk Publisher demo theme*, located at `/src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme` inside your Publisher instance (this theme is distributed as part of Publisher package)
- *The Modern Times theme*, whose Git repo is here: `https://github.com/SuperdeskWebPublisher/theme-dailyNews <https://github.com/SuperdeskWebPublisher/theme-dailyNews/>`_
- *Magazine theme*, whose Git repo is here: `https://github.com/SuperdeskWebPublisher/theme-magazine <https://github.com/SuperdeskWebPublisher/theme-magazine/>`_
