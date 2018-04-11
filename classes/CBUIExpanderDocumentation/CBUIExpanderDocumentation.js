"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIExpanderDocumentation */
/* global
    CBUIExpander,
    Colby */

var CBUIExpanderDocumentation = {

    init: function () {
        let mainElement = document.getElementsByTagName("main")[0];

        {
            let expander = CBUIExpander.create();
            expander.message = `

                No severity specified

            `;
            expander.timestamp = Date.now() / 1000;

            mainElement.appendChild(expander.element);
        }

        let severities = [
            ["Emergency"],
            ["Alert"],
            ["Critical"],
            ["Error"],
            ["Warning"],
            ["Notice"],
            ["Informational"],
            ["Debug"],
        ];

        for (let i = 0; i < 8; i++) {
            let expander = CBUIExpander.create();
            expander.message = `

                Severity ${i}: ${severities[i][0]}

            `;
            expander.severity = i;
            expander.timestamp = Date.now() / 1000;

            mainElement.appendChild(expander.element);
        }
    },
};

Colby.afterDOMContentLoaded(CBUIExpanderDocumentation.init);
