Setting up a new project
========================

Demo Superdesk Publisher themes use Gulp workflow automation (http://gulpjs.com/). 

To correctly set-up working environment for theme development based on one of existing demo themes, you can follow these steps:

- Fork and clone, or just download the theme from GitHub (https://github.com/SuperdeskWebPublisher/theme-dailyNews, https://github.com/SuperdeskWebPublisher/theme-magazine), or use default Publisher theme (which is part of the main repo, https://github.com/superdesk/web-publisher/tree/master/src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme)
- Make sure Gulp is working on your system (how to get it up and running see here: https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md)
- Gulp file is already there in theme. It has all necessary methods already implemented. For development purposes, you can simply fire the task 'watch' and it will automatically a) compile and add all css/scss/sass changes from `public/css/` to `public/dist/style.css`
b) add all js changes from `public/js/` to `public/dist/all.js` file
- For applying changes for production, there is the task 'build' which will also minify css and js and add specific version to these files (to prevent browser caching issues)
- You can also manually run tasks `sass`, `js`, `cssmin`, `jsmin`, `version`, as well as `sw` (service worker steps that ensure propper pre-caching on browser side)

To start from scrach, you should know the theme structure:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        views/                  <=== Views directory
            home.html.twig
        translations/           <=== Translations directory
            messages.en.xlf
            messages.de.xlf
        public/                 <=== Assets directory
            css/
            js/
            images/
        theme.json              <=== Theme configuration

This comes from `SyliusThemeBundle`, "Flexible theming system for Symfony applications".

You may - or may not - use `gulp` to automize your workflow, or can use some other system.

Find more in-depth coverage of this topic in chapter 'Themes'.
