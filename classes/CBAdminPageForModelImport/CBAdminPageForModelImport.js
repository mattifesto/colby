"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdminPageForModelImport */
/* global
    CBUI,
    CBUIActionPart,
    CBUIProcessStatus,
    CBUISectionItem4,
    Colby */

var CBAdminPageForModelImport = {

    /**
     * @return undefined
     */
    DOMContentDidLoad: function() {
        let disabled = false;
        var main = document.getElementsByTagName("main")[0];
        var status = CBUIProcessStatus.create();

        /* import CSV */

        let input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";
        document.body.appendChild(input);

        main.appendChild(CBUI.createHalfSpace());

        let sectionElement = CBUI.createSection();
        let sectionItem = CBUISectionItem4.create();
        sectionItem.callback = function () {
            if (!disabled) {
                input.click();
            }
        };
        let actionPart = CBUIActionPart.create();
        actionPart.title = "Import CSV File...";

        sectionItem.appendPart(actionPart);
        sectionElement.appendChild(sectionItem.element);
        main.appendChild(sectionElement);
        main.appendChild(CBUI.createHalfSpace());
        main.appendChild(status.element);

        input.addEventListener("change", function() {
            disabled = true;
            actionPart.disabled = true;

            Colby.callAjaxFunction("CBAdminPageForModelImport", "uploadDataFile", undefined, input.files[0])
                .then(uploadFulfilled)
                .catch(Colby.displayAndReportError);

            input.value = null;

            /* closure */
            function uploadFulfilled(response) {
                status.processID = response.processID;
                disabled = false;
                actionPart.disabled = false;

                return new Promise(function (resolve, reject) {
                    runNextTask();

                    /* closure */
                    function runNextTask() {
                        Colby.callAjaxFunction("CBTasks2", "runNextTask", {processID: response.processID})
                            .then(runNextTaskFulfilled)
                            .catch(reject);
                    }

                    /* closure */
                    function runNextTaskFulfilled(value) {
                        if (value.taskWasRun) {
                            setTimeout(runNextTask, 0);
                        } else {
                            resolve();
                        }
                    }
                });
            }
        });
    },
};

Colby.afterDOMContentLoaded(CBAdminPageForModelImport.DOMContentDidLoad);
