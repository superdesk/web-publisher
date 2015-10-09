'use strict';

module.exports = {
    app: {
        cwd: 'app',
        src: [
            'scripts/bower_components/superdesk/client/app/scripts/superdesk*/**/*.html',
            'scripts/webpublisher*/**/*.html',
        ],
        dest: 'app/scripts/wp-templates.js',
        options: {
            htmlmin: {
                collapseWhitespace: true,
                collapseBooleanAttributes: true
            },
            bootstrap:  function(module, script) {
                return '"use strict";' +
                    'var wptemplates = angular.module("wp.templates", []);' +
                    'templates.run([\'$templateCache\', function($templateCache) {' +
                    script + ' }]);';
            }
        }
    }
};
