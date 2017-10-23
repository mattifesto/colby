"use strict";
/* jshint strict: global */
/* exported CBDefaultEditor */

var CBDefaultEditor = {

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        var element = document.createElement("div");
        element.className = "CBDefaultEditor";
        var pre = document.createElement("pre");
        pre.textContent = JSON.stringify(args.spec, null, 2);

        element.appendChild(pre);

        return element;
    },
};
