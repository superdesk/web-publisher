Example themes
==============

Superdesk Publisher demo theme
------------------------------

Theme location: `/src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme`

**Publisher Mag** ships within Superdesk Publisher Release, thus can be considered the default and most basic Publisher theme. It serves purpose of showing most common features of the software, such as listing articles, showing article elements and full content, working with menus etc.

To create richer user experience, 3rd-party services can be incorporated. In **Publisher Mag** theme we showcase it with Disqus article comments.

The Modern Times theme
----------------------

Theme repo: `https://github.com/SuperdeskWebPublisher/theme-dailyNews <https://github.com/SuperdeskWebPublisher/theme-dailyNews/>`_ 

**The Modern Times** theme is fresh, fully responsive, highly adaptible Superdesk Publisher theme built primarily to serve those media operations with high daily news production. It offers editors flexibility in ways they can present sets of news (category or tag based; manually curated, fully- or semi-automated content lists; and more).

In this theme we also showcase how 3rd-party services can be incorporated for reacher user experience (Open weather data integration for any weather station in the world, Disqus article comments, Playbuzz voting poll, Google Custom Search Engine).

Magazine theme
--------------

Theme repo: `https://github.com/SuperdeskWebPublisher/theme-magazine <https://github.com/SuperdeskWebPublisher/theme-magazine/>`_

**Magazine** theme is fresh, fully responsive, simple and ultra fast Superdesk Publisher theme built to serve those media operations that are not primarily focused on daily content production, but on fewer stories per day/week that have longer time span. Naturally this applies to traditional weekly, be-weekly or even monthly type of magazines from the print world. 

**Magazine** theme features customizable menu, html and content list widgets which enable live-site editing from frontend.

To create richer user experience, 3rd-party services can be incorporated. In **Magazine** theme we showcase it with Disqus article comments.

PWA theme
---------

**PWA** theme is built as Hybrid app - one React app on both server and client side. It is built on modern and highly optimised code which ensures lightning fast performance.

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
