"use strict";

var CBAdminPageForEditingModels = {

    /**
     * @param   {Object} spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("pre");
        element.textContent = JSON.stringify(args.spec, null, 4);

        return element;
    },

    /**
     * @return  undefined
     */
    handleDOMContentLoaded : function() {
        var formData = new FormData();
        formData.append("ID", CBModelID);

        var xhr     = new XMLHttpRequest();
        xhr.onload  = CBAdminPageForEditingModels.handleModelLoaded.bind(undefined, {
            xhr : xhr
        });
        xhr.onerror = function() {
            alert('An error occured when trying to retreive the data.');
        };

        xhr.open("POST", "/api/?class=CBModels&function=fetchSpec");
        xhr.send(formData);
    },

    /**
     * @param   {XMLHttpRequest} xhr
     *
     * @return  undefined
     */
    handleModelLoaded : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var spec = response.spec || {
                ID          : CBModelID,
                className   : CBModelClassName
            };

            CBAdminPageForEditingModels.renderEditor({
                spec : spec
            });
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param   {Object} spec
     *
     * @return  undefined
     */
    renderEditor : function(args) {
        var main = document.getElementsByTagName("main")[0];

        main.appendChild(CBAdminPageForEditingModels.createEditor({
            spec : args.spec
        }));
    }
}

document.addEventListener("DOMContentLoaded", CBAdminPageForEditingModels.handleDOMContentLoaded);
