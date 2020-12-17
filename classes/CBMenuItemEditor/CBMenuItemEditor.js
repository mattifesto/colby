"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMenuItemEditor */
/* global
    CBUI,
    CBUIStringEditor2,
*/



var CBMenuItemEditor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



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
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBMenuItemEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "name",
                "Name",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "text",
                "Text",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "URL",
                "URL",
                specChangedCallback
            )
        );

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /* -- CBUISpec interfaces -- -- -- -- -- */



    /**
     * @param object spec
     *
     * @return string
     */
    CBUISpec_toDescription(
        spec
    ) {
        return spec.text;
    },
    /* CBUISpec_toDescription() */

};
