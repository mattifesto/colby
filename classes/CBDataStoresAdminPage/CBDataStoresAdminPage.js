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
                    .catch(Colby.displayAndReportError);
            },
        }).element);

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        data.forEach(function (value) {
            section.appendChild(CBUI.createKeyValueSectionItem({
                key: value.className || "No CBModels record",
                value: value.ID,
            }).element);
        });
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;
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
