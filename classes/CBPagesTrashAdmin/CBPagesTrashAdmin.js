"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPagesTrashAdmin */
/* global
    CBUI,
    Colby,
*/

var CBPagesTrashAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(
            CBUI.createHalfSpace()
        );

        mainElement.appendChild(
            CBUI.createSectionHeader(
                {
                    text: "Pages in the trash",
                }
            )
        );

        let sectionElement = CBUI.createSection();

        mainElement.appendChild(sectionElement);

        mainElement.appendChild(
            CBUI.createHalfSpace()
        );

        Colby.callAjaxFunction(
            "CBPagesTrashAdmin",
            "fetchPages"
        ).then(
            function (value) {
                return init_onFulfilled(value);
            }
        ).catch(
            function (error) {
                Colby.displayAndReportError(error);
            }
        );

        return;


        /* -- closures -- -- -- -- -- */

        function init_onFulfilled(pages) {
            pages.forEach(
                function (page) {
                    var sectionItem = CBUI.createSectionItem2();
                    sectionItem.titleElement.textContent = page.title;

                    sectionItem.titleElement.addEventListener(
                        "click",
                        function() {
                            window.location =
                            "/admin/?c=CBModelInspector&ID=" +
                            page.ID;
                        }
                    );

                    sectionElement.appendChild(sectionItem.element);

                    var recoverCommand = document.createElement("div");
                    recoverCommand.className = "command";
                    recoverCommand.textContent = "Recover";

                    recoverCommand.addEventListener(
                        "click",
                        function () {
                            Colby.callAjaxFunction(
                                "CBPagesTrashAdmin",
                                "recoverPage",
                                {
                                    pageID: page.ID,
                                }
                            ).then(
                                function () {
                                    sectionElement.removeChild(
                                        sectionItem.element
                                    );
                                }
                            ).catch(
                                function (error) {
                                    Colby.displayAndReportError(error);
                                }
                            );
                        }
                    );

                    sectionItem.commandsElement.appendChild(recoverCommand);

                    var deleteCommand = document.createElement("div");
                    deleteCommand.className = "command";
                    deleteCommand.textContent = "Delete";

                    deleteCommand.addEventListener(
                        "click",
                        function () {
                            Colby.callAjaxFunction(
                                "CBModels",
                                "deleteByID",
                                {
                                    ID: page.ID
                                }
                            ).then(
                                function () {
                                    sectionElement.removeChild(
                                        sectionItem.element
                                    );

                                    Colby.alert("The page was successfully deleted.");
                                }
                            ).catch(
                                function (error) {
                                    Colby.displayAndReportError(error);
                                }
                            );
                        }
                    );

                    sectionItem.commandsElement.appendChild(deleteCommand);
                }
            );
        }
    },
    /* init() */
};
/* CBPagesTrashAdmin */


Colby.afterDOMContentLoaded(
    function () {
        CBPagesTrashAdmin.init();
    }
);
