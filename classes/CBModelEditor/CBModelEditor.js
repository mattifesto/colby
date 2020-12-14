"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelEditor */
/* global
    CBUI,
    CBUINavigationView,
    CBUIPanel,
    CBUISpecEditor,
    CBUISpecSaver,
    Colby,

    CBModelEditor_originalSpec,
*/



var CBModelEditor = {

    /**
     * @param object spec
     *
     * @return undefined
     */
    renderEditorForSpec: function (spec) {
        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;

        let inspectorURL = (
            "/admin/?c=CBModelInspector&ID=" +
            CBModelEditor_originalSpec.ID
        );

        var specSaver = CBUISpecSaver.create(
            {
                rejectedCallback: CBModelEditor.saveWasRejected,
                spec: spec,
            }
        );

        var navigationView = CBUINavigationView.create();

        main.appendChild(navigationView.element);

        let specEditor = CBUISpecEditor.create(
            {
                spec: spec,
                specChangedCallback: specSaver.specChangedCallback,
            }
        );

        if (specEditor.element === undefined) {
            window.location = inspectorURL;
            return;
        }

        let inspectHeaderItem = CBUI.createHeaderItem();
        inspectHeaderItem.textContent = "Inspect";
        inspectHeaderItem.href = inspectorURL;

        CBUINavigationView.navigate(
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
            CBUIPanel.displayAjaxResponse(error.ajaxResponse);
        } else {
            CBUIPanel.displayText(
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
