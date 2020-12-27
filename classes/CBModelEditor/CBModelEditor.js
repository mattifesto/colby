"use strict";
/* jshint
    esversion: 6,
    strict: global,
    undef: true,
    unused: true
*/
/* exported CBModelEditor */
/* global
    CBSpecSaver,
    CBUI,
    CBUINavigationView,
    CBUIPanel,
    CBUISpecEditor,
    Colby,

    CBModelEditor_originalSpec,
*/



var CBModelEditor = {

    /**
     * @param object spec
     *
     * @return undefined
     */
    renderEditorForSpec: function (
        spec
    ) {
        let mostRecentSavePromise;

        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;

        let inspectorURL = (
            "/admin/?c=CBModelInspector&ID=" +
            CBModelEditor_originalSpec.ID
        );

        let specSaver2 = CBSpecSaver.create(
            spec
        );

        var navigationView = CBUINavigationView.create();

        main.appendChild(navigationView.element);

        let specEditor = CBUISpecEditor.create(
            {
                spec,

                specChangedCallback: function () {
                    let savePromise = specSaver2.CBSpecSaver_save();

                    if (savePromise !== mostRecentSavePromise) {
                        mostRecentSavePromise = savePromise;

                        savePromise.catch(
                            function (error) {
                                CBUIPanel.displayAndReportError(
                                    error
                                );
                            }
                        ).finally(
                            function () {
                                if (savePromise === mostRecentSavePromise) {
                                    mostRecentSavePromise = undefined;
                                }
                            }
                        );
                    }
                },
                /* specChangedCallback */

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
