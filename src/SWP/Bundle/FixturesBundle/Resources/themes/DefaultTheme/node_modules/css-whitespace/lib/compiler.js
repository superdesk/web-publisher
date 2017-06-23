
/**
 * Module dependencies.
 */

var debug = require('debug')('css-whitespace:parser');

/**
 * Compile the given `node`.
 *
 * @param {Array} node
 * @return {String}
 * @api private
 */

module.exports = function(node){
  var indents = 0;
  var rules = [];
  var stash = [];
  var level = 0;
  var nest = 0;

  if (debug.enabled) {
    var util = require('util');
    console.log(util.inspect(node, false, 12, true));
  }

  return visit(node);

  /**
   * Visit `node`.
   */

  function visit(node) {
    switch (node[0]) {
      case 'root':
        return root(node);
      case 'rule':
        if ('@' == node[1][0][0]) ++nest;
        var ret = rule(node);
        if ('@' == node[1][0][0]) --nest;
        return ret;
      case 'block':
        ++level;
        var ret = block(node);
        --level;
        return ret;
      case 'prop':
        return prop(node);
      case 'comment':
        return comment(node);
      default:
        throw new Error('invalid node "' + node[0] + '"');
    }
  }

  /**
   * Visit block.
   */

  function block(node) {
    var buf = [];
    var nodes = node[1];

    for (var i = 0; i < nodes.length; ++i) {
      buf.push(visit(nodes[i]));
    }

    return buf.join('');
  }

  /**
   * Visit comment.
   */

  function comment(node) {
    return indent() + '/*' + node[1] + '*/\n';
  }

  /**
   * Visit prop.
   */

  function prop(node) {
    var prop = node[1];
    var val = node[2];
    return indent() + prop + ': ' + val + ';\n';
  }

  /**
   * Visit rule.
   */

  function rule(node) {
    var font = '@font-face' == node[1][0].trim();
    var rule = node[1];
    var block = node[2];
    var buf = '';

    if (!block) return rule.join('') + ';';

    rules.push(node);

    if ('@' == rule[0][0] && !font) {
      buf = join(rules) + ' {\n';
      visit(block);
      buf += stash.join('\n');
      buf += '\n}';
      stash = [];
    } else if (nest && !font) {
      indents = 1;
      buf = join(rules, 1) + ' {\n';
      indents = 2;
      buf += visit(block);
      buf += '  }';
      indents = 1;
    } else {
      indents = 0;
      buf = join(rules) + ' {\n'
      indents = 1;
      buf += visit(block);
      indents = 0;
      buf += '}';
      if (!hasProperties(block)) buf = '';
    }

    if (rules.length > 1) {
      if (hasProperties(block)) stash.push(buf);
      buf = '';
    }

    rules.pop();

    return buf;
  }

  /**
   * Visit root.
   */

  function root(node) {
    var buf = [];
    for (var i = 0; i < node[1].length; ++i) {
      buf.push(visit(node[1][i]));
      if (stash.length) {
        buf = buf.concat(stash);
        stash = [];
      }
    }
    return buf.join('\n\n');
  }

  /**
   * Join the given rules.
   *
   * @param {Array} rules
   * @param {Number} [offset]
   * @return {String}
   * @api private
   */

  function join(rules, offset) {
    offset = offset || 0;
    var selectors = [];
    var buf = [];
    var curr;
    var next;

    function compile(rules, i) {
      if (offset != i) {
        rules[i][1].forEach(function(selector){
          var parent = ~selector.indexOf('&');
          selector = selector.replace('&', '');
          buf.unshift(parent ? selector : ' ' + selector);
          compile(rules, i - 1);
          buf.shift();
        });
      } else {
        rules[i][1].forEach(function(selector){
          var tail = buf.join('');
          selectors.push(indent() + selector + tail);
        });
      }
    }

    compile(rules, rules.length - 1);

    return selectors.join(',\n');
  }

  /**
   * Return indent.
   */

  function indent() {
    return Array(indents + 1).join('  ');
  }
};

/**
 * Check if `block` has properties.
 *
 * @param {Array} block
 * @return {Boolean}
 * @api private
 */

function hasProperties(block) {
  var nodes = block[1];
  for (var i = 0; i < nodes.length; ++i) {
    if ('prop' == nodes[i][0]) return true;
  }
  return false;
}

/**
 * Blank string filter.
 *
 * @api private
 */

function blank(str) {
  return '' != str;
}
