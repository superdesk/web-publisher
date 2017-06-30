## Inherit [![Build Status](https://travis-ci.org/reworkcss/rework-inherit.png)](https://travis-ci.org/reworkcss/rework-inherit)

Inherit mixin for [rework](https://github.com/visionmedia/rework).
Allows you to inherit __all__ the rules associated with a given selector.

### API

```js
var inherit = require('rework-inherit')

var css = rework(inputCSS)
  .use(inherit())
  .toString()
```

#### Inherit(options{})

Option parameters:

* `propertyRegExp` - Regular expression to match the "inherit" property.
  By default, it is `/^(inherit|extend)s?$/i`, so it matches "inherit", "inherits", "extend", and "extends".
  For example, if you only want to allow the `extend` keyword,
  set the regular expression to `/^extend$/`.

### Examples

#### Regular inherit

```css
.gray {
  color: gray;
}

.text {
  inherit: .gray;
}
```

yields:

```css
.gray,
.text {
  color: gray;
}
```

#### Multiple inherit

Inherit multiple selectors at the same time.

```css
.gray {
  color: gray;
}

.black {
  color: black;
}

.button {
  inherit: .gray, .black;
}
```

yields:

```css
.gray,
.button {
  color: gray;
}

.black,
.button {
  color: black;
}
```

#### Placeholders

Any selector that includes a `%` is considered a placeholder.
Placeholders will not be output in the final CSS.

```css
%gray {
  color: gray;
}

.text {
  inherit: %gray;
}
```

yields:

```css
.text {
  color: gray;
}
```

#### Partial selectors

If you inherit a selector,
all rules that include that selector will be included as well.

```css
div button span {
  color: red;
}

div button {
  color: green;
}

button span {
  color: pink;
}

.button {
  inherit: button;
}

.link {
  inherit: div button;
}
```

yields:

```css
div button span,
div .button span,
.link span {
  color: red;
}

div button,
div .button,
.link {
  color: green;
}

button span,
.button span {
  color: pink;
}
```

#### Chained inheritance

```css
.button {
  background-color: gray;
}

.button-large {
  inherit: .button;
  padding: 10px;
}

.button-large-red {
  inherit: .button-large;
  color: red;
}
```

yields:

```css
.button,
.button-large,
.button-large-red {
  background-color: gray;
}

.button-large,
.button-large-red {
  padding: 10px;
}

.button-large-red {
  color: red;
}
```

#### Media Queries

Inheriting from inside a media query will create a copy of the declarations.
It will act like a "mixin".
Thus, with `%`placeholders, you won't have to use mixins at all.
Each type of media query will need its own declaration,
so there will be some inevitable repetition.

```css
.gray {
  color: gray
}

@media (min-width: 320px) {
  .button {
    inherit: .gray;
  }
}

@media (min-width: 320px) {
  .link {
    inherit: .gray;
  }
}
```

yields:

```css
.gray {
  color: gray;
}

@media (min-width: 320px) {
  .button,
  .link {
    color: gray;
  }
}
```

#### Substitute for Nested Syntax

Rework doesn't support nested syntax.
However, you can simulate nested syntax with inheritance.

```css
.no-touchevents %link-hover:hover {
  text-decoration: underline;
}

a,
button {
  inherit: %link-hover;
}
```

yields:

```css
.no-touchevents a:hover,
.no-touchevents button:hover {
  text-decoration: underline;
}
```

Thus, you have the features of nested syntax without the nesting.

### Limitations

- You can not inherit a rule that is inside a media query;
  you can only inherit rules outside a media query.
  If you find yourself in this situation,
  just use placeholders instead.

### License

The MIT License (MIT)

Copyright (c) 2013 Jonathan Ong me@jongleberry.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.