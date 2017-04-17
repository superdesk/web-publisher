(function() {
    'use strict';


    WebPublisherService.$inject = ['$location', 'gettext', 'metadata', 'api'];
    function WebPublisherService($location, gettext, metadata, api) {
    }

    WebPublisherController.$inject = ['$scope', '$location', 'api', 'notify', 'session'];
    function WebPublisherController($scope, $location, api, notify, session) {
    }

    angular.module('webpublisher.help', [
        'mentio',
        'superdesk.api',
        'superdesk.users',
        'superdesk.desks',
        'superdesk.activity',
        'superdesk.list',
        'superdesk.authoring.metadata',
        'superdesk.keyboard',
        'ui.bootstrap'
    ])
    .service('webpublisher', WebPublisherService)
    .directive('wpHelp', ['asset', function(asset) {
        return {
            templateUrl: 'scripts/webpublisher/views/help.html',
            scope: {},
            link: function(scope) {
            }
        };
    }])
    .config(['superdeskProvider', 'assetProvider', function(superdesk, asset) {
        superdesk.activity('/webpublisher', {
            description: gettext('Find out about web publisher'),
            beta: 1,
            priority: 200,
            category: superdesk.MENU_MAIN,
            label: gettext('Web Publisher'),
            controller: WebPublisherController,
            templateUrl: 'scripts/webpublisher/views/index.html'
        });
    }]);
})();
