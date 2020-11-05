"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelsImportAdmin */
/* global
    CBAjax,
    CBMaintenance,
    CBModelImporter,
    CBModelImporter_processID,
    CBUI,
    CBUIPanel,
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
        let disabled = false;

        let status = CBUIProcessStatus.create();
        status.processID = CBModelImporter_processID;

        let main = document.getElementsByTagName("main")[0];

        main.appendChild(
            CBUI.createHalfSpace()
        );

        initDataFileInputElement();

        {
            let sectionElement = CBUI.createSection();

            initImportButton(sectionElement);

            main.appendChild(sectionElement);
            main.appendChild(CBUI.createHalfSpace());
        }

        main.appendChild(status.element);

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * CBModelsImportAdmin.init() closure
         *
         * @return undefined
         */
        function handleDataFileInputElementChanged() {
            disabled = true;

            importActionPart.element.classList.add("disabled");
            status.clear();

            CBMaintenance.transaction(
                "CBModelsImportAdmin Import",
                function () {
                    return CBAjax.call(
                        "CBModelsImportAdmin",
                        "uploadDataFile",
                        { },
                        dataFileInputElement.files[0]
                    ).then(
                        function () {
                            return Colby.tasks.start();
                        }
                    ).finally(
                        function () {
                            disabled = false;
                            dataFileInputElement.value = null;

                            importActionPart.element.classList.remove(
                                "disabled"
                            );
                        }
                    ).catch(
                        function (error) {
                            CBUIPanel.displayAndReportError(error);
                        }
                    );
                }
            );

            return;
        }
        /* handleDataFileInputElementChanged() */



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

    },
    /* init() */
};



CBModelImporter.initBeforeDOMContentLoaded();

Colby.afterDOMContentLoaded(
    CBModelsImportAdmin.init
);
