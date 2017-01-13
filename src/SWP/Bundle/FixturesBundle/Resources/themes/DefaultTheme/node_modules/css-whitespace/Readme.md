# css-whitespace

  Whitespace significant CSS to regular CSS. Typically used for [Rework](https://github.com/visionmedia/rework),
  however you may use it on its own if you like.

## Installation

```
$ npm install css-whitespace
$ component install visionmedia/css-whitespace
```

## API

```js
var compile = require('css-whitespace');
var css = compile('body\n  color: #888\n');
```

## Example

```css

@charset "utf-8"

@import "foo.css"

body
  padding: 50px
  background: black
  color: white

form
  button
    border-radius: 5px
    padding: 5px 10px

@media print
  body
    padding: 0

  button
    border-radius: 0
    width: 100%
```

yields:

```css
@charset "utf-8";

@import "foo.css";

body {
  padding: 50px;
  background: black;
  color: white;
}

form button {
  border-radius: 5px;
  padding: 5px 10px;
}

@media print {
  body {
    padding: 0;
  }
  button {
    border-radius: 0;
    width: 100%;
  }
}
```

## License

  MIT
