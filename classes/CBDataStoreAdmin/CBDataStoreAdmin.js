"use strict"; /* jshint strict: global */
/* global
    Colby */

var CBDataStoreAdmin = {

    /**
     * @return undefined
     */
    regenerateDocument: function () {
        if (CBDataStoreAdmin.documentIsRegenerating) {
            return;
        }

        CBDataStoreAdmin.partIndex = 0;
        CBDataStoreAdmin.documentIsRegenerating = true;

        var progressElement = document.getElementById('progress');

        progressElement.value = 0;

        CBDataStoreAdmin.regeneratePart();
    },

    /**
     * @return undefined
     */
    regeneratePart: function () {
        Colby.callAjaxFunction("CBDataStoreAdmin", "explore", { index: CBDataStoreAdmin.partIndex })
            .then(onFulfilled)
            .catch(onRejected);

        function onFulfilled(value) {
            CBDataStoreAdmin.partIndex++;

            var progressElement = document.getElementById('progress');

            progressElement.value = CBDataStoreAdmin.partIndex;

            if (CBDataStoreAdmin.partIndex < 256) {
                CBDataStoreAdmin.regeneratePart();
            } else {
                progressElement.value = 0;
                CBDataStoreAdmin.documentIsRegenerating = false;
            }
        }

        function onRejected(error) {
            CBDataStoreAdmin.documentIsRegenerating = false;
            Colby.displayError(error);
        }
    },
};
