"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBCodeAdmin */
/* global
    CBUI,
    Colby,

    CBCodeAdmin_results,
*/

var CBCodeAdmin = {

    /**
     * @return Element
     */
    createRootElement: function () {
        let element = CBUI.createElement("CBUIRoot");

        let pre = document.createElement("pre");

        element.appendChild(pre);

        pre.textContent = CBCodeAdmin_results.join("\n");

        return element;
    },
    /* createRootElement() */


    /**
     * @return undefined
     */
    init: function () {
        let elements = document.getElementsByClassName("CBCodeAdmin");

        if (elements.length > 0) {
            let element = elements.item(0);

            element.appendChild(
                CBCodeAdmin.createRootElement()
            );
        }
    },
    /* init() */
};
/* CBCodeAdmin */


Colby.afterDOMContentLoaded(
    function () {
        CBCodeAdmin.init();
    }
);
