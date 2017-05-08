DESCRIPTION
-----------

**Publisher Mag** ships within Superdesk Publisher Release, thus can be considered the default and most basic Publisher theme. It serves purpose of showing most common features of the software, such as listing articles, showing article elements and full content, working with menu widgets etc.

**Publisher Mag** also features html and content list widgets which enable live-site editing from frontend.

To create richer user experience, 3rd-party services can be incorporated. In **Publisher Mag** theme we showcase it with Disqus article comments.

SETTING UP DEVELOPMENT ENVIRONMENT
----------------------------------

For information and explanation on the theme structure, please see http://superdesk-publisher.readthedocs.io/en/latest/manual/themes/index.html 

This Superdesk Publisher theme uses Gulp workflow automation (http://gulpjs.com/). 

Being default Superdesk Publisher theme, it doesn't exist as separate repo; instead it is part of the software release and can be found on this path: `/src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme`

To correclty set-up working environment for theme development, you can follow these steps:

- Fork and clone, or just download Web Publisher from GitHub (https://github.com/superdesk/web-publisher)
- Make sure Gulp is working on your system (how to get it up and running see here: https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md)
- Gulp file is already there in theme's root. It has all necessary methods already implemented. For development purposes, you can simply fire the task 'watch' and it will automatically a) compile and add all css/scss/sass changes from `public/css/` to `public/dist/style.css`
b) add all js changes from `public/js/` to `public/dist/all.js` file
- For applying changes for production, there is the task 'build' which will also minify css and js and add specific version to these files (to prevent browser caching issues)
- You can also manually run tasks `sass`, `js`, `cssmin`, `jsmin`, `version`, as well as `sw` (service worker steps that ensure propper pre-caching on browser side)

ADJUSTING AND CUSTOMIZING THEME
-------------------------------
**Publisher Mag** theme comes with predefined functionality which includes:
- front page with automated list of three latest articles
- list of articles with specific criteria under it 
- category pages with pagination
- article page with featured image on top, article content and article image slider under it
- static page template 
- Theme has built-in support for Google AMP (accelerated mobile pages). These templates are in subfolder `/amp`. More information on Google AMP project is here: https://www.ampproject.org/

For theme templates customization please refer to Superdesk Publisher documentation, starting here: http://superdesk-publisher.readthedocs.io/en/latest/manual/templates_system/index.html
