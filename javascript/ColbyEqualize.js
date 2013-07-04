"use strict";

/**
 * Avoid errors when calling `console.log` on older browsers.
 */

if (!window.console)
{
    var console = {};

    console.log = function()
    {
    };

    window.console = console;
}
