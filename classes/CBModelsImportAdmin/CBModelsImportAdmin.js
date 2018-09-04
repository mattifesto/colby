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
        let dataFileInputElement;
        let saveUnchangedModels = false;
        let disabled = false;
        var main = document.getElementsByTagName("main")[0];
        var status = CBUIProcessStatus.create();

        initDateFileInputElement();

        /* import CSV */


        main.appendChild(CBUI.createHalfSpace());

        let importActionPart = CBUIStringsPart.create();

        {
            let sectionElement = CBUI.createSection();

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    if (!disabled) {
                        dataFileInputElement.click();
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

        dataFileInputElement.addEventListener("change", function() {
            disabled = true;
            importActionPart.element.classList.add("disabled");

            Colby.callAjaxFunction(
                "CBModelsImportAdmin",
                "uploadDataFile",
                {
                    saveUnchangedModels: saveUnchangedModels,
                },
                dataFileInputElement.files[0]
            )
            .then(uploadFulfilled)
            .catch(Colby.displayAndReportError);

            dataFileInputElement.value = null;

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

        /**
         * CBModelsImportAdmin.init() closure
         *
         *      Creates and initializes the closure dataFileInputElement
         *      variable.
         *
         * @return undefined
         */
        function initDateFileInputElement() {
            dataFileInputElement = document.createElement("input");
            dataFileInputElement.type = "file";
            dataFileInputElement.style.display = "none";
            document.body.appendChild(dataFileInputElement);
        }
    },
};

Colby.afterDOMContentLoaded(CBModelsImportAdmin.init);
