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
        let importActionPart;
        let saveUnchangedModels = false;
        let disabled = false;
        var status = CBUIProcessStatus.create();

        let main = document.getElementsByTagName("main")[0];
        main.appendChild(CBUI.createHalfSpace());

        initDataFileInputElement();

        {
            let sectionElement = CBUI.createSection();

            initImportButton(sectionElement);
            initSaveUnchangedModelsSwitch(sectionElement);

            main.appendChild(sectionElement);
            main.appendChild(CBUI.createHalfSpace());
        }

        main.appendChild(status.element);

        return;

        /**
         * CBModelsImportAdmin.init() closure
         *
         * @return undefined
         */
        function handleDataFileInputElementChanged() {
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
        }

        /**
         * CBModelsImportAdmin.init() closure
         *
         *      Creates and initializes the closure dataFileInputElement
         *      variable.
         *
         * @return undefined
         */
        function initDataFileInputElement() {
            dataFileInputElement = document.createElement("input");
            dataFileInputElement.type = "file";
            dataFileInputElement.style.display = "none";
            document.body.appendChild(dataFileInputElement);

            dataFileInputElement.addEventListener(
                "change",
                handleDataFileInputElementChanged
            );
        }

        /**
         * CBModelsImportAdmin.init() closure
         *
         *      Creates the import button and and initializes the closure
         *      importActionPart variable.
         *
         * @param Element sectionElement
         *
         * @return undefined
         */
        function initImportButton(sectionElement) {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                if (!disabled) {
                    dataFileInputElement.click();
                }
            };

            importActionPart = CBUIStringsPart.create();
            importActionPart.string1 = "Import CSV File...";
            importActionPart.element.classList.add("action");

            sectionItem.appendPart(importActionPart);
            sectionElement.appendChild(sectionItem.element);
        }

        /**
         * CBModelsImportAdmin.init() closure
         *
         *      Creates the "save unchanged models" switch.
         *
         * @param Element sectionElement
         *
         * @return undefined
         */
        function initSaveUnchangedModelsSwitch(sectionElement) {
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
    },
};

Colby.afterDOMContentLoaded(CBModelsImportAdmin.init);
