"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLinkView1Editor */
/* globals
    CBAjax,
    CBException,
    CBImage,
    CBModel,
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUIStringEditor2,
*/


var CBLinkView1Editor = {

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
        let spec = CBModel.valueAsModel(
            args,
            "spec"
        );

        if (spec === undefined) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The spec parameter is not a valid model."
                ),
                args,
                "72f17a59d7931caff1744c8f09caa1029926fd76"
            );
        }

        let specChangedCallback = CBModel.valueAsFunction(
            args,
            "specChangedCallback"
        );

        if (spec === undefined) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The specChangedCallback parameter is not a valid function."
                ),
                args,
                "6250bd96166fa932a06a710932b99cfbe6e188e6"
            );
        }

        let elements = CBUI.createElementTree(
            "CBLinkView1Editor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = createEditor_handleImageChosen;
        imageChooser.removed = createEditor_handleImageRemoved;

        imageChooser.src = CBImage.toURL(
            spec.image,
            "rw960"
        );

        let sectionItemElement = CBUI.createSectionItem();

        sectionItemElement.appendChild(
            imageChooser.element
        );

        sectionElement.appendChild(
            sectionItemElement
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "title",
                "Title",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "description",
                "Description",
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

        sectionElement.appendChild(
            CBUISelector.create(
                {
                    labelText: "Size",
                    options: [
                        {title: "Small", value: "small"},
                        {title: "Medium", value: undefined},
                        {title: "Large", value: "large"},
                    ],
                    propertyName: "size",
                    spec: spec,
                    specChangedCallback: specChangedCallback,
                }
            ).element
        );

        return element;



        /* -- closures -- -- -- -- -- */



        /**
         * @param object chooserArgs
         *
         * @return undefined
         */
        function createEditor_handleImageChosen() {
            CBAjax.call(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            ).then(
                function (imageModel) {
                    spec.image = imageModel;

                    specChangedCallback();

                    imageChooser.src = CBImage.toURL(imageModel, "rw960");
                }
            );
        }
        /* createEditor_handleImageChosen() */



        /**
         * @return undefined
         */
        function createEditor_handleImageRemoved() {
            spec.image = undefined;
            specChangedCallback();
        }
        /* createEditor_handleImageRemoved() */

    },
    /* CBUISpecEditor_createEditorElement() */

};
/* CBLinkView1Editor */
