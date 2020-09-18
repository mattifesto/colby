"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBAjax,
    CBImage,
    CBUI,
    CBUIImageChooser,
    CBUIPanel,
*/


(function () {

    window.CBArtworkEditor = {
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
            "CBArtworkEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];


        /* image chooser */

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = handleImageChosen;
        imageChooser.removed = handleImageRemoved;

        sectionElement.appendChild(
            imageChooser.element
        );


        /* set image choose thumbnail */

        if (spec.image) {
            imageChooser.src = CBImage.toURL(
                spec.image,
                "rw960"
            );
        }


        /* done */

        return element;



        /* -- closures -- */



        /**
         * @return undefined
         */
        function handleImageChosen() {
            imageChooser.caption = "uploading...";

            CBAjax.call(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            ).then(
                function (imageModel) {
                    spec.image = imageModel;

                    specChangedCallback();

                    imageChooser.src = CBImage.toURL(
                        imageModel,
                        "rw960"
                    );
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(
                        error
                    );
                }
            ).finally(
                function () {
                    imageChooser.caption = "";
                }
            );
        }
        /* handleImageChosen() */



        /**
         * @return undefined
         */
        function handleImageRemoved() {
            spec.image = undefined;
            specChangedCallback();
        }
        /* handleImageRemoved() */

    }
    /* createEditorElement() */

})();
