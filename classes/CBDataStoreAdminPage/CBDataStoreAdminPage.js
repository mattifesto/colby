"use strict";
/* jshint strict: global */
/* global
    CBUI,
    Colby */

var CBDataStoreAdminPage = {

    /**
     * @param hex160 ID
     *
     * @return undefined
     */
    deleteModel: function (ID) {
        if (window.confirm("Are you sure you want to permanently remove all data related to this model and data store?")) {
            Colby.callAjaxFunction("CBModels", "deleteByID", { ID: ID })
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);
        }

        function onFulfilled() {
            alert("The model was successfully deleted.");
            location.reload(true);
        }
    },

    /**
     * @return undefined
     */
    init: function () {
        var elements = document.getElementsByClassName("CBDataStoreAdminPage_delete");

        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];

            if (!element.classList.contains("built")) {
                var button = CBUI.createButton({
                    text: "Delete Model",
                    callback: CBDataStoreAdminPage.deleteModel.bind(undefined, element.dataset.id),
                });

                element.appendChild(button.element);

                element.classList.add("built");
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBDataStoreAdminPage.init);
