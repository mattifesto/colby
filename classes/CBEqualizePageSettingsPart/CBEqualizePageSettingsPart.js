"use strict";
/* jshint strict: global */

/**
 * For IE 11
 * From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isFinite
 */
Number.isFinite = Number.isFinite || function(value) {
    return typeof value === 'number' && isFinite(value);
};

/**
 * For IE 11
 * From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isInteger
 */
Number.isInteger = Number.isInteger || function(value) {
  return typeof value === 'number' &&
    isFinite(value) &&
    Math.floor(value) === value;
};
