"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    CBUIActionPart,
    CBUINavigationArrowPart,
    CBUINavigationView,
    CBUIPanel,
    CBUISectionItem4,
    CBUISelector,
    CBUITitleAndDescriptionPart,
    Colby,
*/



(function () {

    Colby.afterDOMContentLoaded(afterDOMContentLoaded);



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName(
            "CBDataStoresAdminPage"
        );

        if (elements.length < 1) {
            return;
        }

        let mainElement = elements.item(0);

        Colby.callAjaxFunction(
            "CBDataStoresAdminPage",
            "fetchData"
        ).then(
            function (value) {
                mainElement.appendChild(
                    createElement(value)
                );
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function createElement(
        data
    ) {
        var element = document.createElement("div");

        element.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();

            sectionItem.callback = function () {
                Colby.callAjaxFunction(
                    "CBDataStoresFinderTask",
                    "restart"
                ).then(
                    function () {
                        CBUIPanel.displayText(
                            "The data store finder has been restarted."
                        );
                    }
                ).catch(
                    function (error) {
                        CBErrorHandler.displayAndReport(error);
                    }
                );
            };

            let actionPart = CBUIActionPart.create();
            actionPart.title = "Restart Data Store Finder";

            sectionItem.appendPart(actionPart);
            sectionElement.appendChild(sectionItem.element);
            element.appendChild(sectionElement);
            element.appendChild(CBUI.createHalfSpace());
        }

        var classNames = new Set();

        data.forEach(function (value) {
            classNames.add(value.className);
        });

        var options = [];

        classNames.forEach(function (className) {
            var title = className;

            if (!className) {
                title = "No model";
                className = undefined;
            }

            options.push({
                title: title,
                value: className
            });
        });

        {
            let sectionElement = CBUI.createSection();

            sectionElement.appendChild(CBUISelector.create({
                labelText: "Class Name",
                options: options,
                propertyName: "className",
                valueChangedCallback: update,
            }).element);
            element.appendChild(sectionElement);
            element.appendChild(CBUI.createHalfSpace());
        }

        var dataStoresSection = CBUI.createSection();

        update();

        element.appendChild(dataStoresSection);
        element.appendChild(CBUI.createHalfSpace());

        var navigationView = CBUINavigationView.create();

        CBUINavigationView.navigate(
            {
                element: element,
                title: "Data Stores",
            }
        );

        return navigationView.element;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function update(className) {
            if (!className) {
                className = null;
            }

            dataStoresSection.textContent = "";

            data.forEach(function (value) {
                if (value.className === className) {
                    let sectionItem = CBUISectionItem4.create();

                    sectionItem.callback = function () {
                        window.location = (
                            "/admin/?c=CBModelInspector&ID=" +
                            value.ID
                        );
                    };

                    let titleAndDescriptionPart =
                    CBUITitleAndDescriptionPart.create();

                    titleAndDescriptionPart.title = value.ID;
                    titleAndDescriptionPart.description = (
                        value.className || "No model"
                    );

                    sectionItem.appendPart(titleAndDescriptionPart);
                    sectionItem.appendPart(CBUINavigationArrowPart.create());

                    dataStoresSection.appendChild(sectionItem.element);
                }
            });
        }
        /* update() */
    }
    /* createElement() */

})();
