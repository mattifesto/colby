"use strict";
/* jshint strict: global */
/* jshint esnext: true */
/* exported CBDataStoresAdminPage */
/* global
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

var CBDataStoresAdminPage = {

    /**
     * @return Element
     */
    createElement: function (data) {
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
                        CBUIPanel.displayError(error);
                        Colby.reportError(error);
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
    },
    /* createElement() */


    /**
     * @return undefined
     */
    init: function () {
        Colby.callAjaxFunction(
            "CBDataStoresAdminPage",
            "fetchData"
        ).then(
            function (value) {
                let element = CBDataStoresAdminPage.createElement(value);
                document.getElementsByTagName("main")[0].appendChild(element);
            }
        ).catch(
            function (error) {
                CBUIPanel.displayError(error);
                Colby.reportError(error);
            }
        );
    },
};

Colby.afterDOMContentLoaded(CBDataStoresAdminPage.init);
