"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdminPageForModelImport */
/* global
    CBUI,
    CBUIActionLink,
    CBUIProcessStatus,
    Colby */

var CBAdminPageForModelImport = {

    /**
     * @return undefined
     */
    DOMContentDidLoad: function() {
        var section, item;
        var main = document.getElementsByTagName("main")[0];
        var status = CBUIProcessStatus.create();

        /* import JSON */

        main.appendChild(CBUI.createHalfSpace());
        main.appendChild(CBUI.createSectionHeader({
            paragraphs: ["Import Single Model"],
        }));
        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        var jsonInputElement = document.createElement("input");
        jsonInputElement.type = "file";
        jsonInputElement.style.display = "none";
        var jsonActionLink = CBUIActionLink.create({
            callback: jsonInputElement.click.bind(jsonInputElement),
            labelText: "Import JSON File...",
        });

        jsonInputElement.addEventListener("change", function() {
            var formData = new FormData();
            formData.append("file", jsonInputElement.files[0]);

            Colby.fetchAjaxResponse("/api/?class=CBAdminPageForModelImport&function=importJSON", formData)
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);

            jsonInputElement.value = null;

            function onFulfilled(response) {
                status.processID = response.processID;
            }
        });

        item.appendChild(jsonInputElement);
        item.appendChild(jsonActionLink.element);
        section.appendChild(item);

        main.appendChild(section);

        /* import CSV */

        main.appendChild(CBUI.createHalfSpace());
        main.appendChild(CBUI.createSectionHeader({
            paragraphs: ["Import Multiple Models"],
        }));

        section = CBUI.createSection();
        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";
        section.appendChild(input);

        var actionLink = CBUIActionLink.create({
            "callback" : input.click.bind(input),
            "labelText" : "Import CSV File...",
        });

        input.addEventListener("change", function() {
            actionLink.disableCallback();

            var formData = new FormData();
            formData.append("dataFile", input.files[0]);

            input.value = null;

            Colby.fetchAjaxResponse("/api/?class=CBAdminPageForModelImport&function=uploadDataFile", formData)
                .then(uploadFulfilled)
                .catch(Colby.displayAndReportError);

            /* closure */
            function uploadFulfilled(response) {
                status.processID = response.processID;
                actionLink.enableCallback();

                return new Promise(function (resolve, reject) {
                    dispatch();

                    /* closure */
                    function dispatch() {
                        Colby.callAjaxFunction("CBTasks2", "dispatchNextTask", {processID: response.processID})
                            .then(dispatchFulfilled)
                            .catch(reject);
                    }

                    /* closure */
                    function dispatchFulfilled(dispatchResponse) {
                        if (dispatchResponse.taskWasDispatched) {
                            setTimeout(dispatch, 0);
                        } else {
                            resolve();
                        }
                    }
                });
            }
        });

        item = CBUI.createSectionItem();
        item.appendChild(actionLink.element);
        section.appendChild(item);
        main.appendChild(section);

        main.appendChild(CBUI.createHalfSpace());

        main.appendChild(status.element);
    },
};

Colby.afterDOMContentLoaded(CBAdminPageForModelImport.DOMContentDidLoad);
