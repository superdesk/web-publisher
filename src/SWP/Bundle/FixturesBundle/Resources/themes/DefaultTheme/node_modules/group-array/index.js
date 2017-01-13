/*!
 * group-array <https://github.com/doowb/group-array>
 *
 * Copyright (c) 2015, Brian Woodward.
 * Licensed under the MIT License.
 */

'use strict';

var typeOf = require('kind-of');
var get = require('get-value');

function groupFn(arr, props) {
  if (arr == null) {
    return [];
  }

  if (!Array.isArray(arr)) {
    throw new TypeError('group-array expects an array.');
  }

  if (arguments.length === 1) {
    return arr;
  }

  props = flatten([].slice.call(arguments, 1));
  var groups = groupBy(arr, props.shift());

  while (props.length) {
    subGroup(groups, props.shift());
  }
  return groups;
}

function groupBy(arr, prop, key) {
  var len = arr.length, i = -1;
  var groups = {};

  while (++i < len) {
    var obj = arr[i];
    var val;

    // allow a function to modify the object
    // and/or return a val to use
    if (typeof prop === 'function') {
      val = prop.call(groups, obj, key);
    } else {
      val = get(obj, prop);
    }

    if (typeof val === 'string' || typeof val === 'number') {
      groups[val] = groups[val] || [];
      groups[val].push(obj);
    } else if (typeOf(val) === 'object') {
      groupObject(groups, obj, val);
    } else if (Array.isArray(val)) {
      groupArray(groups, obj, val);
    } else if (typeOf(val) === 'function') {
      throw new Error('group-array expects group keys to be strings, objects or undefined: ' + key);
    }
  }
  return groups;
}

function groupObject(groups, obj, val) {
  for (var k in val) {
    if (val.hasOwnProperty(k)) {
      groups[k] = groups[k] || [];
      groups[k].push(obj);
    }
  }
}

function groupArray(groups, obj, val) {
  val.forEach(function (item) {
    groups[item] = groups[item] || [];
    groups[item].push(obj);
  });
}

function subGroup(groups, prop) {
  for (var key in groups) {
    if (groups.hasOwnProperty(key)) {
      var val = groups[key];
      if (!Array.isArray(val)) {
        groups[key] = subGroup(val, prop);
      } else {
        groups[key] = groupBy(val, prop, key);
      }
    }
  }
  return groups;
}

/**
 * Flatten the given array.
 */

function flatten(arr) {
  return [].concat.apply([], arr);
}

/**
 * Expose `groupArray`
 */

module.exports = groupFn;
