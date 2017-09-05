"use strict"; /* jshint strict: global */
/* global
    CBUI,
    Colby */

var CBDataStoresAdminPage = {

    /**
     * @return Element
     */
    createElement: function (data) {
        var section;
        var element = document.createElement("div");

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createButton({
            text: "Start Fast Find",
            callback: function () {
                Colby.callAjaxFunction("CBDataStoresFinderTask", "startFastFind")
                    .then(onFulfilled)
                    .catch(Colby.displayAndReportError);
            },
        }).element);

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        data.forEach(function (value) {
            var item = CBUI.createKeyValueSectionItem({
                key: value.className || "No CBModels record",
                value: value.ID,
            });

            item.element.style.cursor = "pointer";
            item.element.addEventListener("click", function () {
                window.location = "/admin/documents/view/?ID=" + value.ID;
            });
            section.appendChild(item.element);
        });
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;

        function onFulfilled() {
            Colby.alert("The fast find process has been started.");
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
