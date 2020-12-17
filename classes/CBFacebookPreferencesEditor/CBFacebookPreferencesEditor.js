"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBSitePreferencesEditor */
/* globals
    CBUI,
    CBUIStringEditor2,
*/



(function () {

    window.CBFacebookPreferencesEditor = {
        CBUISpecEditor_createEditorElement,
    };



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    function CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBFacebookPreferencesEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let editorElement = elements[0];
        let sectionElement = elements[2];

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "appID",
                "Facebook App ID",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "appSecret",
                "Facebook App Secret",
                specChangedCallback
            )
        );

        return editorElement;
    }
    /* CBUISpecEditor_createEditorElement() */

})();
