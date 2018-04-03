"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelEditor */
/* global
    CBModelEditor_message,
    CBModelEditor_originalSpec,
    Colby,
    CBUI,
    CBUIMessagePart,
    CBUINavigationView,
    CBUISectionItem4,
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

        if (CBModelEditor_originalSpec) {
            CBModelEditor.renderEditorForSpec(CBModelEditor_originalSpec);
        } else {
            var main = document.getElementsByTagName("main")[0];

            main.appendChild(CBUI.createHalfSpace());

            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();
            let messagePart = CBUIMessagePart.create();
            messagePart.message = CBModelEditor_message;

            sectionItem.appendPart(messagePart);
            sectionElement.appendChild(sectionItem.element);
            main.appendChild(sectionElement);

            main.appendChild(CBUI.createHalfSpace());
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
                window.location = "/admin/?c=CBModelInspector&ID=" + CBModelEditor_originalSpec.ID;
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
