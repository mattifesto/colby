"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBModel,
    CBUI,
    CBUISpecArrayEditor,
*/


(function () {

    window.CBArtworkCollectionEditor = {
        CBUISpecEditor_createEditorElement: createEditorElement,
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
    function createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBArtworkCollectionEditor"
        );

        let element = elements[0];


        /* spec array editor */

        let artworks = CBModel.valueToArray(
            spec,
            'artworks'
        );

        spec.artworks = artworks;

        let artworksEditor = CBUISpecArrayEditor.create(
            {
                specs: artworks,
                specsChangedCallback: specChangedCallback,
                addableClassNames: [
                    "CBArtwork",
                ],
            }
        );

        element.appendChild(
            artworksEditor.element
        );


        /* done */

        return element;
    }
    /* createEditorElement() */

})();
