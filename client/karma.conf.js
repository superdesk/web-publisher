'use strict';

module.exports = function(config) {
    config.set({
        frameworks: [
            'jasmine',
            'requirejs'
        ],

        preprocessors: {
            '**/*.html': ['ng-html2js'],
            'app/scripts/!(bower_components)/*.js': ['coverage']
        },

        // list of files / patterns to load in the browser
        files: [
            'test-main.js',
            'app/scripts/bower_components/jquery/dist/jquery.js',
            'app/scripts/bower_components/angular/angular.js',
            {pattern: 'app/**/*.js', included: false},
            {pattern: 'app/scripts/webpublisher--*/**/*[sS]pec.js', included: false},
            'app/scripts/webpublisher-*/**/*.html',
            'app/scripts/bower_components/superdesk/client/app/scripts/superdesk-*/**/*.html'
        ],

        // list of files to exclude
        exclude: [
            'app/scripts/bower_components/**/*[sS]pec.js',
            'app/main.js'
        ],

        ngHtml2JsPreprocessor: {
            stripPrefix: 'app/',
            moduleName: 'templates'
        },

        junitReporter: {
            outputFile: 'test-results.xml'
        },

        // test results reporter to use
        reporters: ['dots'],

        // web server port
        port: 8080,

        // cli runner port
        runnerPort: 9100,

        // enable / disable watching file and executing tests whenever any file changes
        autoWatch: true,

        // Start these browsers, currently available:
        browsers: ['Chrome'],

        // Continuous Integration mode
        singleRun: false
    });
};
