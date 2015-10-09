'use strict';

var tests = [];
var APP_SPEC_REG_EXP = /^\/base\/app\/scripts\/(.*)\.js$/;

for (var file in window.__karma__.files) {
    if (window.__karma__.files.hasOwnProperty(file)) {
        if (/[sS]pec\.js$/.test(file)) {
            var matches = APP_SPEC_REG_EXP.exec(file);
            if (matches && matches.length === 2) {
                tests.push(matches[1]);
            } else {
                tests.push(file);
            }
        }
    }
}

function superdesk_push(path) {
    return tests.push('bower_components/superdesk/client/app/scripts/' + path);
}

// superdesk core
superdesk_push('superdesk/mocks');
superdesk_push('superdesk/api/api');
superdesk_push('superdesk/auth/auth');
superdesk_push('superdesk/menu/menu');
superdesk_push('superdesk/config/config');
superdesk_push('superdesk/editor/editor');
superdesk_push('superdesk/notify/notify');
superdesk_push('superdesk/activity/activity');
superdesk_push('superdesk/menu/notifications/notifications');
superdesk_push('superdesk/services/translate');
superdesk_push('superdesk/services/modalService');
superdesk_push('superdesk/services/preferencesService');
superdesk_push('superdesk/features/features');
superdesk_push('superdesk/services/asset');
superdesk_push('superdesk/privileges/privileges');
superdesk_push('superdesk/notification/notification');
superdesk_push('superdesk/itemList/itemList');
superdesk_push('superdesk/ui/ui');
superdesk_push('superdesk/upload/upload');
superdesk_push('superdesk/list/list');
superdesk_push('superdesk/keyboard/keyboard');
superdesk_push('superdesk/services/storage');
superdesk_push('superdesk/beta/beta');

// superdesk apps
superdesk_push('superdesk-authoring/authoring');
superdesk_push('superdesk-authoring/widgets/widgets');
superdesk_push('superdesk-authoring/comments/comments');
superdesk_push('superdesk-authoring/workqueue/workqueue');
superdesk_push('superdesk-authoring/metadata/metadata');
superdesk_push('superdesk-authoring/versioning/versioning');
superdesk_push('superdesk-authoring/versioning/versions/versions');
superdesk_push('superdesk-authoring/versioning/history/history');
superdesk_push('superdesk-authoring/packages/packages');
superdesk_push('superdesk-authoring/editor/find-replace');
superdesk_push('superdesk-authoring/multiedit/multiedit');
superdesk_push('superdesk-authoring/macros/macros');
superdesk_push('superdesk-workspace/content/content');
superdesk_push('superdesk-packaging/packaging');
superdesk_push('superdesk-desks/desks');
superdesk_push('superdesk-desks/aggregate');
superdesk_push('superdesk-desks/aggregate-widget/aggregate');
superdesk_push('superdesk-groups/groups');
superdesk_push('superdesk-search/search');
superdesk_push('superdesk-legal-archive/legal_archive');

superdesk_push('superdesk-ingest/module');

superdesk_push('superdesk-users/users');
superdesk_push('superdesk-users/profile');
superdesk_push('superdesk-users/activity/activity');
superdesk_push('superdesk-users/import/import');

superdesk_push('superdesk-dashboard/module');
superdesk_push('superdesk-dashboard/workspace-tasks/tasks');

superdesk_push('superdesk-archive/module');
superdesk_push('superdesk-archive/directives');

superdesk_push('superdesk-dictionaries/dictionaries');
superdesk_push('superdesk-publish/publish');
superdesk_push('superdesk-publish/filters/publish-filters');
superdesk_push('superdesk/editor/spellcheck/spellcheck');
superdesk_push('superdesk-templates/templates');
superdesk_push('superdesk-highlights/highlights');
superdesk_push('superdesk-monitoring/monitoring');
superdesk_push('superdesk-workspace/workspace');

////////////////////////////////////////////////////////////////////////////////
// WebPublisher app
////////////////////////////////////////////////////////////////////////////////
tests.push('webpublisher/module');
////////////////////////////////////////////////////////////////////////////////

// libs
tests.push('bower_components/ment.io/dist/mentio');
tests.push('angular-ui');
tests.push('angular-route');
tests.push('angular-file-upload');
tests.push('moment');
//
tests.push('bower_components/ngmap/build/scripts/ng-map');


requirejs.config({
    baseUrl: '/base/app/scripts',
    deps: ['angular-mocks', 'angular-gettext', 'gettext', 'angular'],

    callback: function() {
        require(tests, window.__karma__.start);
    },

    paths: {
        jquery: 'bower_components/jquery/dist/jquery',
        bootstrap: 'bower_components/bootstrap/js',
        angular: 'bower_components/angular/angular',
        moment: 'bower_components/momentjs/moment',
        lodash: 'bower_components/lodash/lodash',
        d3: 'bower_components/d3/d3',
        'angular-resource': 'bower_components/angular-resource/angular-resource',
        'angular-gettext': 'bower_components/angular-gettext/dist/angular-gettext',
        'angular-route': 'bower_components/angular-route/angular-route',
        'angular-mocks': 'bower_components/angular-mocks/angular-mocks',
        'angular-ui': 'bower_components/angular-bootstrap/ui-bootstrap-tpls',
        'angular-file-upload': 'bower_components/ng-file-upload/angular-file-upload',
        'moment-timezone': 'bower_components/moment-timezone/moment-timezone',
    },

    shim: {
        jquery: {
            exports: 'jQuery'
        },

        angular: {
            exports: 'angular',
            deps: ['jquery']
        },

        'angular-resource': ['angular'],
        'angular-gettext': ['angular'],
        'angular-route': ['angular'],
        'angular-mocks': ['angular'],
        'angular-ui': ['angular'],
        'angular-file-upload': ['angular']
    }
});
