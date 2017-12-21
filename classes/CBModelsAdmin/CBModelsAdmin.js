"use strict";
/* jshint strict: global */
/* exported CBModelsAdmin */
/* globals
    CBUI,
    CBModelsAdmin_modelClassName,
    CBModelsAdmin_modelList,
    CBModelsAdmin_page,
    Colby */

var CBModelsAdmin = {

    /**
     * @return undefined
     */
    initialize: function () {
        if (CBModelsAdmin_page !== "list") {
            return;
        }

        var mainElement = document.getElementsByTagName("main")[0];
        var titleElement = document.createElement("div");
        titleElement.textContent = CBModelsAdmin_modelClassName + " Models";
        var newElement = document.createElement("div");

        newElement.addEventListener("click", function () {
            window.location = "/admin/page/?class=CBAdminPageForEditingModels&className=" +
                              encodeURIComponent(CBModelsAdmin_modelClassName);
        });

        mainElement.appendChild(CBUI.createHeader({
            centerElement: titleElement,
            rightElements: [newElement],
        }));

        mainElement.appendChild(CBUI.createHalfSpace());

        if (CBModelsAdmin_modelList.length > 0) {
            var section = CBUI.createSection();

            CBModelsAdmin_modelList.forEach(function (model) {
                var item = CBUI.createSectionItem2();

                item.titleElement.textContent = model.title;

                item.titleElement.addEventListener("click", function () {
                    window.location = "/admin/page/?class=CBAdminPageForEditingModels&ID=" +
                                      encodeURIComponent(model.ID);
                });

                /* export */
                var exportCommandElement = document.createElement("div");
                exportCommandElement.className = "command";
                exportCommandElement.textContent = "Export";

                exportCommandElement.addEventListener("click", function () {
                    window.location = "/admin/models/export/?ID=" +
                                      encodeURIComponent(model.ID);
                });

                item.commandsElement.appendChild(exportCommandElement);

                /* inspect */
                var inspectCommandElement = document.createElement("div");
                inspectCommandElement.className = "command";
                inspectCommandElement.textContent = "Inspect";

                inspectCommandElement.addEventListener("click", function () {
                    window.location = "/admin/?c=CBModelInspector&ID=" +
                                      encodeURIComponent(model.ID);
                });

                item.commandsElement.appendChild(inspectCommandElement);

                section.appendChild(item.element);
            });

            mainElement.appendChild(section);
            mainElement.appendChild(CBUI.createHalfSpace());
        }
    },
};

Colby.afterDOMContentLoaded(CBModelsAdmin.initialize);
