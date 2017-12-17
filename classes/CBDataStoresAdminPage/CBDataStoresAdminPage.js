"use strict";
/* jshint strict: global */
/* jshint esnext: true */
/* exported CBDataStoresAdminPage */
/* global
    CBUI,
    CBUINavigationView,
    CBUISelector,
    Colby */

var CBDataStoresAdminPage = {

    /**
     * @return Element
     */
    createElement: function (data) {
        var section, item;
        var element = document.createElement("div");

        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback : function () {},
            rootItem : {
                element : element,
                title : "Data Stores",
            },
        });

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createButton({
            text: "Restart Data Store Finder",
            callback: function () {
                Colby.callAjaxFunction("CBDataStoresFinderTask", "restart")
                    .then(function () { Colby.alert("The data store finder has been restarted."); })
                    .catch(Colby.displayAndReportError);
            },
        }).element);


        var classNames = new Set();

        data.forEach(function (value) {
            classNames.add(value.className);
        });

        var options = [{
            title: "None",
        }];

        classNames.forEach(function (className) {
            var title = className;

            if (!className) {
                title = "No CBModel";
                className = undefined;
            }

            options.push({
                title: title,
                value: className
            });
        });

        element.appendChild(CBUI.createHalfSpace());
        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText: "Class Name",
            options: options,
            navigateToItemCallback: navigationView.navigateToItemCallback,
            propertyName: "className",
            valueChangedCallback: update,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());
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
                    var item = CBUI.createKeyValueSectionItem({
                        key: value.className || "No CBModels record",
                        value: value.ID,
                    });

                    item.element.style.cursor = "pointer";
                    item.element.addEventListener("click", function () {
                        window.location = "/admin/?c=CBModelInspector&ID=" + value.ID;
                    });
                    dataStoresSection.appendChild(item.element);
                }
            });
        }
    },

    /**
     * @return undefined
     */
    init: function() {
        Colby.callAjaxFunction("CBDataStoresAdminPage", "fetchData")
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        function onFulfilled(value) {
            var element = CBDataStoresAdminPage.createElement(value);
            document.getElementsByTagName("main")[0].appendChild(element);
        }
    }
};

Colby.afterDOMContentLoaded(CBDataStoresAdminPage.init);
