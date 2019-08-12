"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelEditor */
/* global
    CBUI,
    CBUINavigationView,
    CBUISpecEditor,
    CBUISpecSaver,
    Colby,

    CBModelEditor_originalSpec,
*/

var CBModelEditor = {

    /**
     * @param object spec
     *
     * @return Element
     */
    createEditor: function (args) {
        var element = document.createElement("pre");

        element.textContent = JSON.stringify(
            args.spec,
            null,
            4
        );

        return element;
    },
    /* createEditor() */


    /**
     * @param object spec
     *
     * @return undefined
     */
    renderEditorForSpec: function (spec) {
        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;

        var specSaver = CBUISpecSaver.create(
            {
                rejectedCallback: CBModelEditor.saveWasRejected,
                spec: spec,
            }
        );

        var navigationView = CBUINavigationView.create(
            {
                defaultSpecChangedCallback: specSaver.specChangedCallback,
            }
        );

        main.appendChild(navigationView.element);

        let specEditor = CBUISpecEditor.create(
            {
                navigateToItemCallback: navigationView.navigateToItemCallback,
                spec: spec,
                specChangedCallback: specSaver.specChangedCallback,
            }
        );

        let inspectHeaderItem = CBUI.createHeaderItem();
        inspectHeaderItem.textContent = "Inspect";

        inspectHeaderItem.href = (
            "/admin/?c=CBModelInspector&ID=" +
            CBModelEditor_originalSpec.ID
        );

        navigationView.navigateToItemCallback.call(
            undefined,
            {
                element: specEditor.element,
                rightElements: [inspectHeaderItem.element],
                title: spec.className + " Editor",
            }
        );
    },
    /* renderEditorForSpec() */


    /**
     * @param Error error
     *
     * @return Promise (rejected)
     */
    saveWasRejected: function (error) {
        if (error.ajaxResponse) {
            Colby.displayResponse(error.ajaxResponse);
        } else {
            Colby.alert(
                error.message ||
                (
                    "CBModelEditor.saveWasRejected(): " +
                    "No error message was provided."
                )
            );
        }

        return Promise.reject(error);
    },
    /* saveWasRejected() */
};


Colby.afterDOMContentLoaded(
    function afterDOMContentLoaded() {
        if (window.CBModelEditorAuthorizationFailed) {
            return;
        }

        CBModelEditor.renderEditorForSpec(
            CBModelEditor_originalSpec
        );
    }
);
