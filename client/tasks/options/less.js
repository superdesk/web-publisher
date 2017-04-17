/**
 * This file is part of Superdesk.
 *
 * Copyright 2013, 2014 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code, or
 * at https://www.sourcefabric.org/superdesk/license
 */

var files = [
    {
        dest: '<%= distDir %>/styles/css/verifiedpixel.css',
        src: '<%= appDir %>/styles/less/verifiedpixel.less'
    }, {
        dest: '<%= distDir %>/styles/css/vp-bootstrap.css',
        src: '<%= appDir %>/styles/less/vp-bootstrap.less'
    }, {
        expand: true,
        dest: '<%= tmpDir %>/',
        cwd: '<%= appDir %>/scripts/',
        src: ['verifiedpixel-*/**/*.less'],
        ext: '.css'
    },
    {
        expand: true,
        dest: '<%= tmpDir %>/',
        cwd: '<%= appDir %>/scripts/bower_components/superdesk/client/app/scripts',
        src: ['superdesk/**/*.less', 'superdesk-*/**/*.less'],
        ext: '.css'
    }
];

module.exports = {
    dev: {
        options: {
            paths: ['<%= appDir %>/styles/less', '<%= appDir %>/scripts/bower_components/superdesk/client/app/styles/less'],
            compress: false,
            cleancss: true,
            relativeUrls: true
        },
        files: files
    },
    prod: {
        options: {
            paths: ['<%= appDir %>/styles/less', '<%= appDir %>/scripts/bower_components/superdesk/client/app/styles/less'],
            compress: false,
            cleancss: true
        },
        files: files
    }
};
