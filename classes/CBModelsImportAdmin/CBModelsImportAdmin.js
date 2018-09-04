"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelsImportAdmin */
/* global
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIProcessStatus,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby
*/

var CBModelsImportAdmin = {

    /**
     * @return undefined
     */
    init: function() {
        let saveUnchangedModels = false;
        let disabled = false;
        var main = document.getElementsByTagName("main")[0];
        var status = CBUIProcessStatus.create();

        /* import CSV */

        let input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";
        document.body.appendChild(input);

        main.appendChild(CBUI.createHalfSpace());

        let importActionPart = CBUIStringsPart.create();

        {
            let sectionElement = CBUI.createSection();

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    if (!disabled) {
                        input.click();
                    }
                };

                importActionPart.string1 = "Import CSV File...";
                importActionPart.element.classList.add("action");

                sectionItem.appendPart(importActionPart);
                sectionElement.appendChild(sectionItem.element);
            }

            {
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Save Unchanged Models";
                let switchPart = CBUIBooleanSwitchPart.create();
                switchPart.changed = function () {
                    saveUnchangedModels = switchPart.value;
                };
                sectionItem.appendPart(stringsPart);
                sectionItem.appendPart(switchPart);
                sectionElement.appendChild(sectionItem.element);
            }

            main.appendChild(sectionElement);
            main.appendChild(CBUI.createHalfSpace());
        }

        main.appendChild(status.element);

        input.addEventListener("change", function() {
            disabled = true;
            importActionPart.element.classList.add("disabled");

            Colby.callAjaxFunction(
                "CBModelsImportAdmin",
                "uploadDataFile",
                {
                    saveUnchangedModels: saveUnchangedModels,
                },
                input.files[0]
            )
            .then(uploadFulfilled)
            .catch(Colby.displayAndReportError);

            input.value = null;

            /* closure */
            function uploadFulfilled(response) {
                status.processID = response.processID;
                window.setTimeout(function () {
                    disabled = false;
                    importActionPart.element.classList.remove("disabled");
                }, 2000);

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

Colby.afterDOMContentLoaded(CBModelsImportAdmin.init);
