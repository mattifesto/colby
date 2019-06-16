"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBGitStatusAdmin */
/* global
    CBUI,
    CBUIExpander,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,
*/

var CBGitStatusAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        var mainElement = document.getElementsByTagName("main")[0];
        var timeoutID;

        mainElement.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();
            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Refresh";
            stringsPart.element.classList.add("action");

            sectionItem.callback = init_fetchStatus;

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }

        var container = document.createElement("div");
        container.className = "CBGitStatusAdmin_statusContainer";

        mainElement.appendChild(container);

        init_fetchStatus();

        return;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function init_fetchStatus() {
            if (timeoutID) {
                window.clearTimeout(timeoutID);
                timeoutID = undefined;
            }

            Colby.callAjaxFunction(
                "CBGitStatusAdmin",
                "fetchStatus"
            ).then(
                function (value) {
                    container.textContent = "";

                    value.forEach(
                        function (status) {
                            let expander = CBUIExpander.create();
                            expander.expanded = true;
                            expander.timestamp = Date.now() / 1000;
                            expander.message = status.message || "";

                            container.appendChild(expander.element);
                        }
                    );

                    Colby.updateTimes(true);

                    timeoutID = window.setTimeout(
                        init_fetchStatus,
                        30000
                    );
                }
            ).catch(
                function (error) {
                    Colby.displayAndReportError(error);
                }
            );
        }
        /* init_fetchStatus() */
    },
    /* init() */
};
/* CBGitStatusAdmin */


Colby.afterDOMContentLoaded(
    function () {
        CBGitStatusAdmin.init();
    }
);
