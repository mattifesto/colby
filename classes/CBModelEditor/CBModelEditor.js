"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelEditor */
/* global
    CBModelEditor_modelClassName,
    CBModelEditor_modelID,
    Colby,
    CBUI,
    CBUINavigationView,
    CBUISpecEditor,
    CBUISpecSaver */

var CBModelEditor = {

    /**
     * @param object spec
     *
     * @return Element
     */
    createEditor: function (args) {
        var element = document.createElement("pre");
        element.textContent = JSON.stringify(args.spec, null, 4);

        return element;
    },

    /**
     * @return undefined
     */
    handleDOMContentLoaded: function () {
        if (window.CBModelEditorAuthorizationFailed) {
            return;
        }

        Colby.callAjaxFunction("CBModels", "fetchSpec", {ID: CBModelEditor_modelID})
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        /* closure */
        function onFulfilled(value) {
            let spec =  value.spec;

            if (spec === undefined) {
                spec = {
                    className: CBModelEditor_modelClassName,
                    ID: CBModelEditor_modelID,
                };
            }

            CBModelEditor.renderEditorForSpec(spec);
        }
    },

    /**
     * @param object spec
     *
     * @return undefined
     */
    renderEditorForSpec: function (spec) {
        var element = document.createElement("div");
        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;
        var specSaver = CBUISpecSaver.create({
            rejectedCallback: CBModelEditor.saveWasRejected,
            spec: spec,
        });
        var specChangedCallback = specSaver.specChangedCallback;
        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback: specChangedCallback,
        });

        element.appendChild(CBUISpecEditor.create({
            navigateToItemCallback: navigationView.navigateToItemCallback,
            spec: spec,
            specChangedCallback: specChangedCallback,
        }).element);

        var inspectHeaderButtonItem = CBUI.createHeaderButtonItem({
            callback: function () {
                window.location = "/admin/?c=CBModelInspector&ID=" + CBModelEditor_modelID;
            },
            text: "Inspect",
        });

        navigationView.navigateToItemCallback.call(undefined, {
            element: element,
            rightElements: [inspectHeaderButtonItem],
            title: spec.className + " Editor",
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
            Colby.alert(error.message || "CBModelEditor.saveWasRejected(): No error message was provided.");
        }

        return Promise.reject(error);
    },
};

Colby.afterDOMContentLoaded(CBModelEditor.handleDOMContentLoaded);
