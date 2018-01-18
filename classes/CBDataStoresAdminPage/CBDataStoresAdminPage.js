"use strict";
/* jshint strict: global */
/* jshint esnext: true */
/* exported CBDataStoresAdminPage */
/* global
    CBUI,
    CBUIActionPart,
    CBUINavigationArrowPart,
    CBUINavigationView,
    CBUISectionItem4,
    CBUISelector,
    CBUITitleAndDescriptionPart,
    Colby */

var CBDataStoresAdminPage = {

    /**
     * @return Element
     */
    createElement: function (data) {
        var element = document.createElement("div");
        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback : function () {},
            rootItem : {
                element : element,
                title : "Data Stores",
            },
        });

        element.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                Colby.callAjaxFunction("CBDataStoresFinderTask", "restart")
                    .then(function () { Colby.alert("The data store finder has been restarted."); })
                    .catch(Colby.displayAndReportError);
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
                navigateToItemCallback: navigationView.navigateToItemCallback,
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

        return navigationView.element;

        function update(className) {
            //Colby.alert(className);

            if (!className) {
                className = null;
            }

            dataStoresSection.textContent = "";

            data.forEach(function (value) {
                if (value.className === className) {
                    let sectionItem = CBUISectionItem4.create();
                    sectionItem.callback = function () {
                        window.location = "/admin/?c=CBModelInspector&ID=" + value.ID;
                    };
                    let titleAndDescriptionPart = CBUITitleAndDescriptionPart.create();
                    titleAndDescriptionPart.title = value.ID;
                    titleAndDescriptionPart.description = value.className || "No model";

                    sectionItem.appendPart(titleAndDescriptionPart);
                    sectionItem.appendPart(CBUINavigationArrowPart.create());

                    dataStoresSection.appendChild(sectionItem.element);
                }
            });
        }
    },

    /**
     * @return undefined
     */
    init: function () {
        Colby.callAjaxFunction("CBDataStoresAdminPage", "fetchData")
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        function onFulfilled(value) {
            var element = CBDataStoresAdminPage.createElement(value);
            document.getElementsByTagName("main")[0].appendChild(element);
        }
    },
};

Colby.afterDOMContentLoaded(CBDataStoresAdminPage.init);
