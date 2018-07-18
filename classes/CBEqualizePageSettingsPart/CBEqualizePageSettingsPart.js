"use strict";
/* jshint strict: global */

/**
 * For IE 11
 */
Number.isFinite = Number.isFinite || function(value) {
    return typeof value === 'number' && isFinite(value);
};
