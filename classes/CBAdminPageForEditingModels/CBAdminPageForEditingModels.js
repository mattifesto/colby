"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* globals
    CBModelClassInfo,
    CBModelClassName,
    CBModelID,
    Colby,
    CBUI,
    CBUINavigationView,
    CBUISpecEditor,
    CBUISpecSaver */


var CBAdminPageForEditingModels = {

    /**
     * @param   {Object} spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element = document.createElement("pre");
        element.textContent = JSON.stringify(args.spec, null, 4);

        return element;
    },

    /**
     * @param {Object} args.spec
     *
     * @return {Element}
     */
    createHeader : function(args) {
        var element = document.createElement("div");
        element.className = "CBUIHeader";
        var left = document.createElement("div");
        left.className = "left";
        var center = document.createElement("div");
        center.className = "center";
        center.textContent = (window.CBModelClassInfo ? CBModelClassInfo.singularTitle : args.spec.className) + " Editor";
        var right = document.createElement("div");
        right.className = "right";

        element.appendChild(left);
        element.appendChild(center);
        element.appendChild(right);

        return element;
    },

    /**
     * @return  undefined
     */
    handleDOMContentLoaded : function() {
        if (window.CBAdminPageForEditingModelsAuthorizationFailed) {
            return;
        }

        var formData = new FormData();
        formData.append("className", CBModelClassName);
        formData.append("ID", CBModelID);

        var xhr = new XMLHttpRequest();
        xhr.onload = CBAdminPageForEditingModels.handleModelLoaded.bind(undefined, {
            xhr : xhr
        });
        xhr.onerror = function() {
            alert('An error occured when trying to retreive the data.');
        };

        xhr.open("POST", "/api/?class=CBModels&function=fetchSpec");
        xhr.send(formData);
    },

    /**
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleModelLoaded : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var spec = response.spec || { ID : CBModelID, className : CBModelClassName };
            CBAdminPageForEditingModels.renderEditorForSpec(spec);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param object spec
     *
     * @return undefined
     */
    renderEditorForSpec : function (spec) {
        var element = document.createElement("div");
        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;
        var specSaver = CBUISpecSaver.create({
            rejectedCallback: CBAdminPageForEditingModels.saveWasRejected,
            spec: spec,
        });
        var specChangedCallback = specSaver.specChangedCallback;
        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback : specChangedCallback,
        });

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUISpecEditor.create({
            navigateCallback : navigationView.navigateToSpecCallback,
            navigateToItemCallback : navigationView.navigateToItemCallback,
            spec : spec,
            specChangedCallback : specChangedCallback,
        }).element);
        element.appendChild(CBUI.createHalfSpace());

        navigationView.navigateToItemCallback.call(undefined, {
            element : element,
            title : spec.className + " Editor",
        });

        main.appendChild(navigationView.element);
    },

    /**
     * @param Error error
     *
     * @return Promise (rejected)
     */
    saveWasRejected: function (error) {
        if (error.ajaxResponse) {
            Colby.displayResponse(error.ajaxResponse);
        } else {
            Colby.alert(error.message || "CBAdminPageForEditingModels.saveWasRejected(): No error message was provided.");
        }

        return Promise.reject(error);
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForEditingModels.handleDOMContentLoaded);
