"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBDefaultEditor */
/* global
    CBUI,
*/



var CBDefaultEditor = {

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

        let elements = CBUI.createElementTree(
            "CBDefaultEditor",
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBDefaultEditor_content CBUI_text1"
        );

        let editorElement = elements[0];
        let contentElement = elements[3];

        contentElement.textContent = `

            There is no editor available for ${spec.className} specs.

        `;

        return editorElement;
    },
    /* CBUISpecEditor_createEditorElement() */

};
