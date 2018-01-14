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
        if (CBModelsAdmin_page !== "modelList") {
            return;
        }

        var mainElement = document.getElementsByTagName("main")[0];
        var titleElement = document.createElement("div");
        titleElement.textContent = CBModelsAdmin_modelClassName + " Models";
        var newElement = document.createElement("div");

        newElement.addEventListener("click", function () {
            window.location = "/admin/?c=CBModelEditor&className=" +
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
                var sectionItem = CBUI.createSectionItem3();

                sectionItem.callback = function () {
                    window.location = "/admin/?c=CBModelEditor&ID=" +
                                      encodeURIComponent(model.ID);
                };

                var titleAndDescriptionPart = CBUI.createTitleAndDescriptionSectionItemPart();
                var title = model.title ? model.title.trim() : '';

                if (title === "") {
                    title = CBModelsAdmin_modelClassName + " (no title)";
                }

                titleAndDescriptionPart.title = title;
                titleAndDescriptionPart.description = model.ID;

                sectionItem.appendPart(titleAndDescriptionPart);

                sectionItem.appendPart(CBUINavigationArrowPart.create());

                /* export
                var exportCommandElement = document.createElement("div");
                exportCommandElement.className = "command";
                exportCommandElement.textContent = "Export";

                exportCommandElement.addEventListener("click", function () {
                    window.location = "/admin/models/export/?ID=" +
                                      encodeURIComponent(model.ID);
                });

                item.commandsElement.appendChild(exportCommandElement);

                /* inspect
                var inspectCommandElement = document.createElement("div");
                inspectCommandElement.className = "command";
                inspectCommandElement.textContent = "Inspect";

                inspectCommandElement.addEventListener("click", function () {
                    window.location = "/admin/?c=CBModelInspector&ID=" +
                                      encodeURIComponent(model.ID);
                });

                item.commandsElement.appendChild(inspectCommandElement);
                */

                section.appendChild(sectionItem.element);
            });

            mainElement.appendChild(section);
            mainElement.appendChild(CBUI.createHalfSpace());
        }
    },
};

Colby.afterDOMContentLoaded(CBModelsAdmin.initialize);
