gulp-import-css
===============

Import several css files into a single file, one by one, rebasing urls and inlining @import

## Install

Install with [npm](https://npmjs.org/package/gulp-import-css).

```
npm install --save-dev gulp-import-css
```

## Examples

Let's say you have `assets/reset.css`:

```css
body {margin: 0}
```

`assets/home.css`:

```css
@import url('reset.css');
/* Important: can't be @import 'reset.css' */
.home {font-size: 14px; }
```

After `gulp` you get `dist/home.css`:

```css
body {
  margin: 0;
}

/* Important: can't be @import 'reset.css' */

.home {
  font-size: 14px;
}
```

This is the `Gulpfile.js`:

```js
var gulp = require('gulp');
var importCss = require('gulp-import-css');

gulp.task('default', function () {
  gulp.src('assets/home.css')
    .pipe(importCss())
    .pipe(gulp.dest('dist/'));
});
```

Now, run the command `gulp` to get the combined css file.

## License

[MIT](http://en.wikipedia.org/wiki/MIT_License) @ yuguo
