About themes and multitenancy
-----------------------------

Themes provide templates for the customization of the appearance and functionality of your public-facing websites. Themes can also be translated to support multiple written languages and regional dialects.

There are two Superdesk Web Publisher themes systems; default one is built on top of fast, flexible and easy to use `Twig <http://twig.sensiolabs.org/>`_ templates. Alternativelly, `PWA <https://web.dev/progressive-web-apps/>`_ Next.js (React/Node.js) Superdesk Publisher renderer can be used.

Twig - Publisher's default theme system 
```````````````````````````````````````
By default, themes are located under the :code:`app/themes` directory. A basic theme must have the following structure:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        views/                  <=== Views directory
            home.html.twig
        translations/           <=== Translations directory
            messages.en.xlf
            messages.de.xlf
        screenshots/                <=== Theme screenshots
            front.jpg
        public/                 <=== Assets directory
            css/
            js/
            images/
        theme.json              <=== Theme configuration

Publisher does not support the option of `Sylius theme structure`_ to have bundle resources nested inside a theme.

Progressive Web App (PWA) Themes 
````````````````````````````````

PWA theme, on the other hand, is built as Hybrid app - one React app on both server and client side. It is built on modern and highly optimised code which ensures lightning fast performance.

Our PWA solution is Server Side Generated (SSG, not SSR - server side rendered) and Client Side Rendered (CSR, React) - on build, app renders pages to HTML and JSON. It refreshes these files during runtime on defined schedule. The end users ALWAYS get a static file - either HTML (on initial load) or JSON (when navigating between pages), with data needed to render given page on client side.

Beside standard front - section - article page functionality, and tag - author - search pages, default Publisher's PWA theme also includes:

- Responsiveness - fits any form factor: desktop, mobile, tablet, or whatever is next. It makes a project available to more people on more devices with wildly varying operating systems, browser capabilities, system APIs, and screen sizes. It ensures that websites work on any device that can access the web, regardless of a browser.
- app-like experiences which users enjoy using. Also, it allow users to add the app to their home screen. With the option to install websites, users are offered the ability to install PWA and easily access it on their home screens without the hassle of an app store.
- integration of Web Vitals recording into Google Analytics (that way one gets real data from users about page speed and other measurements that can be then visualised in Analytics using `custom dashboard <https://analytics.google.com/analytics/web/template?uid=H4hQiuJlTvKuzvajY86Fsw/>`_ or `online app <https://web-vitals-report.web.app/>`_. (`More about web vitals <https://web.dev/vitals/>`_) 
- Publisher Analytics: app reports views back to publisher endpoint
- Static/Dynamic sitemaps and sitemap-news 0.9
- Installable as an app on mobiles and even on desktop Chrome
- Possibility of offline usage, thanks to service workers and manifest.json
- AMP support out of the box
- Re-engagement - PWAs feature Push Notifications is used for promotions and specials, as those updates can be displayed to the users even if they don’t have the PWA installed or a browser tab open to the website.
- Sentry integration
- User Login/Register

* * *

Superdesk Publisher can serve many websites from one instance, and each website *tenant* can have multiple themes. The active theme for the tenant can be selected with a local settings file, or by API.

If only one tenant is used, which is the default, the theme should be placed under the :code:`app/themes/default` directory, (e.g. :code:`app/themes/default/ExampleTheme`).

If there were another tenant configured, for example :code:`client1`, the files for one of this tenant's themes could be placed under the :code:`app/themes/client1/ExampleTheme` directory.

* * *

Superdesk Publisher's default theme system Twig provides an easy way to create device-specific templates. This means you only need to put the elements in a particular template which are going to be used on the target device.

The supported device types are: :code:`desktop, phone, tablet, plain`.

A theme with device-specific templates could be structured like this:

.. code-block:: bash

    ExampleTheme/                   <=== Theme starts here
        phone                       <=== Views used on phones
            views/                  <=== Views directory
                home.html.twig
        tablet                      <=== Views used on tablets
            views/                  <=== Views directory
                home.html.twig
        views/                      <=== Default templates directory
            home.html.twig
        translations/               <=== Translations directory
            messages.en.xlf
            messages.de.xlf
        screenshots/                <=== Theme screenshots
            front.jpg
        public/                     <=== Assets directory
            css/
            js/
            images/
        theme.json                  <=== Theme configuration


.. note::

     If a device is not recognized by the Publisher, it will fall back to the :code:`desktop` type. If there is no :code:`desktop` directory with the required template file, the locator will try to load the template from the root level :code:`views` directory.

     More details about theme structure and configuration can be found in the `Sylius Theme Bundle documentation`_.

.. _Sylius Theme Bundle documentation: http://docs.sylius.org/en/latest/bundles/SyliusThemeBundle/your_first_theme.html

.. _Sylius Theme structure: http://docs.sylius.org/en/latest/bundles/SyliusThemeBundle/your_first_theme.html#theme-structure
