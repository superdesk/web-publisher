'use strict';
var gutil = require('gulp-util'),
  path = require('path'),
  fs = require('fs'),
  rework = require('rework'),
  reworkImporter = require('rework-importer'),
  through = require('through2');

// Consts
var PLUGIN_NAME = 'gulp-import-css';

module.exports = function() {

  return through.obj(function(file, enc, cb) {
    if (file.isStream()) {
      this.emit('error', new gutil.PluginError(PLUGIN_NAME, 'Streaming not supported'));
      return cb();
    }


    try {
      var processedCss = rework(String(file.contents), 'utf-8')
        .use(reworkImporter({
          path: file.path,
          base: file.base,
          preProcess: function(ast, options) {
            return ast
                .use(rework.url(function(url) {
                    var srcDir,
                      resourcePath,
                      destDir;

                    if (isAbsoluteUrl(url) || isRootRelativeUrl(url)) {
                      return url;
                    }

                    // rebase relative url(...) found in CSS to be imported
                    // @import url(...) handled by rework-importer; not passed through here

                    srcDir = path.dirname(options.path);
                    resourcePath = path.resolve(srcDir, url);
                    destDir = path.dirname(file.path);

                    return path.relative(destDir, resourcePath);
                }));
          }
        }))
        .toString();
    } catch(err) {
      this.emit('error', new gutil.PluginError(PLUGIN_NAME, err));
      return cb();
    }

    file.contents = new Buffer(processedCss);
    this.push(file);
    cb();
  });
};

function isAbsoluteUrl(url) {
  return (/^[\w]+:\/\/./).test(url);
}

function isRootRelativeUrl(url) {
  return url.charAt(0) === '/';
}
