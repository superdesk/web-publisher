# Rework Importer

[Rework](https://npmjs.org/package/rework) plugin for allowing CSS files to
import other CSS files.

## Usage

To properly use the import plugin you should make sure it's the first you call
use on. This allows your other plugins to do their work on the imported CSS.

```javascript
var rework = require('rework');
var imprt  = require('rework-importer');
var fs     = require('fs');

// Not recommended way of loading styles...
rework(fs.readFileSync('style.css'), 'utf-8')
  .use(imprt({
    path: 'style.css',
    base: __dirname + '/styles'
  })) // opts described below
  .use(another-plugin)
  .use(another-plugin)
  .toString();
```

**Many imports in one block.**

_style.css_

```css
@import {
  file: myFirstCSSFile.css;
  file: mySecondCSSFile.css
}
```

_myFirstCSSFile.css_

```css
body {
  background: #000;
}
```

_myFirstCSSFile.css_

```css
h1 {
  font-size: 200px;
}
```

_Resulting CSS_

```css
body {
  background: #000;
}

h1 {
  font-size: 200px;
}
```

**Or with _"native"_ `@import`-syntax:**

_style.css_
```css
@import url('foobar.css');
body {
  background: #000;
}
```

_foobar.css_
```css
* { box-sizing: border-box; }
```

_Resulting CSS_
```css
* {
  box-sizing: border-box;
}

body {
  background: #000;
}
```

## Options

Available options are:

* **path**, **Required**, Path to the parsed file. This will be used to calculate relative `@imports` 
* **base**, Defaults to the current working directory. Path to the base directory, all absolute urls (starting with "/") will be relative to this path.
* **whitespace**, set to true if you want to use [significant
whitespace](https://npmjs.org/package/css-whitespace) in your imported files.
* **encoding**, if your CSS is anything other then UTF-8 encoded.

## Known issues

* Imports currently, probably, only work at the "top level" stylesheet. Not
inside `@keyframes` or `@media` declarations.`

## License

MIT

