"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBBackgroundViewEditor */
/* global
    CBErrorHandler,
    CBImage,
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUISpec,
    CBUISpecArrayEditor,
    CBUIStringEditor,
    Colby,

    CBBackgroundViewEditor_addableClassNames,
*/



var CBBackgroundViewEditor = {

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

        CBBackgroundViewEditor.prepareSpec(
            spec
        );

        let elements, sectionItemElement, sectionElement;

        elements = CBUI.createElementTree(
            "CBBackgroundViewEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        sectionElement = elements[2];

        /* title */
        {
            let titleEditor = CBUIStringEditor.create();
            titleEditor.title = "Title";

            titleEditor.value = CBModel.valueToString(
                spec,
                "title"
            );

            titleEditor.changed = function () {
                spec.title = titleEditor.value;
                specChangedCallback();
            };

            sectionElement.appendChild(
                titleEditor.element
            );
        }
        /* title */


        /* color */
        {
            let colorEditor = CBUIStringEditor.create();
            colorEditor.title = "Background Color";

            colorEditor.value = CBModel.valueToString(
                spec,
                "color"
            );

            colorEditor.changed = function () {
                spec.color = colorEditor.value;
                specChangedCallback();
            };

            sectionElement.appendChild(
                colorEditor.element
            );
        }
        /* color */


        /* repeat horizontally */

        sectionItemElement = CBUI.createSectionItem();

        sectionItemElement.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Repeat Horizontally",
                    propertyName: "imageShouldRepeatHorizontally",
                    spec: spec,
                    specChangedCallback: specChangedCallback,
                }
            ).element
        );

        sectionElement.appendChild(
            sectionItemElement
        );


        /* repeat vertically */

        sectionItemElement = CBUI.createSectionItem();

        sectionItemElement.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Repeat Vertically",
                    propertyName: "imageShouldRepeatVertically",
                    spec: spec,
                    specChangedCallback: specChangedCallback,
                }
            ).element
        );

        sectionElement.appendChild(
            sectionItemElement
        );


        /* minimum view height is image height */

        sectionItemElement = CBUI.createSectionItem();

        sectionItemElement.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Minimum View Height is Image Height",
                    propertyName: "minimumViewHeightIsImageHeight",
                    spec: spec,
                    specChangedCallback: specChangedCallback,
                }
            ).element
        );

        sectionElement.appendChild(
            sectionItemElement
        );


        /* subviews */
        {
            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: CBBackgroundViewEditor_addableClassNames,
                    specs: spec.children,
                    specsChangedCallback: specChangedCallback,
                }
            );

            editor.title = "Subviews";

            element.appendChild(
                editor.element
            );

            element.appendChild(
                CBUI.createHalfSpace()
            );
        }
        /* subviews */


        /* background image */

        {
            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent = "Background Image";

            element.appendChild(
                titleElement
            );
        }


        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = createEditor_imageWasChosen;
        imageChooser.removed = createEditor_imageWasRemoved;

        updateImagePreview();

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBUI_sectionItem"
        );

        element.appendChild(
            elements[0]
        );

        sectionItemElement = elements[2];

        sectionItemElement.appendChild(
            imageChooser.element
        );

        return element;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function createEditor_imageWasChosen() {
            Colby.callAjaxFunction(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            ).then(
                function (imageModel) {
                    spec.image = imageModel;
                    spec.imageHeight = imageModel.height;
                    spec.imageWidth = imageModel.width;
                    spec.imageURL = CBImage.toURL(
                        imageModel
                    );

                    updateImagePreview();

                    specChangedCallback();
                }
            ).catch(
                function (error) {
                    CBErrorHandler.displayAndReport(error);
                }
            );
        }
        /* createEditor_imageWasChosen() */



        /**
         * @return undefined
         */
        function createEditor_imageWasRemoved() {
            spec.image = undefined;
            spec.imageHeight = undefined;
            spec.imageWidth = undefined;
            spec.imageURL = undefined;

            updateImagePreview();

            specChangedCallback();
        }
        /* createEditor_imageWasRemoved() */



        /**
         * @return undefined
         */
        function updateImagePreview() {
            if (spec.imageURL) {
                if (spec.image) {
                    imageChooser.src = CBImage.toURL(
                        spec.image,
                        'rw960'
                    );
                } else {
                    imageChooser.src = spec.imageURL;
                }

                imageChooser.caption = (
                    spec.imageWidth +
                    "px × " +
                    spec.imageHeight +
                    "px"
                );
            } else {
                imageChooser.src = "";
                imageChooser.caption = "";
            }
        }
        /* updateImagePreview */

    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @return undefined
     */
    prepareSpec: function (spec) {
        if (!spec.children) {
            spec.children = [];
        }

        if (spec.minimumViewHeightIsImageHeight === undefined) {
            spec.minimumViewHeightIsImageHeight = true;
        }
    },
    /* prepareSpec() */



    /* -- CBUISpec interfaces -- -- -- -- -- */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let title = CBModel.valueToString(spec, "title").trim();

        if (title !== "") {
            return title;
        } else {
            if (Array.isArray(spec.children)) {
                for (let i = 0; i < spec.children.length; i++) {
                    let description = CBUISpec.specToDescription(
                        spec.children[i]
                    );

                    if (description) {
                        return description;
                    }
                }
            }
        }
    },
    /* CBUISpec_toDescription() */



    /* -- CBUISpec interfaces -- -- -- -- -- */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toThumbnailURL: function (spec) {
        if (spec.image) {
            return CBImage.toURL(
                spec.image,
                'rw320'
            );
        } else if (spec.imageURL) {
            return spec.imageURL;
        } else {
            if (Array.isArray(spec.children)) {
                for (let i = 0; i < spec.children.length; i++) {
                    let thumbnailURI = CBUISpec.specToThumbnailURL(
                        spec.children[i]
                    );

                    if (thumbnailURI) {
                        return thumbnailURI;
                    }
                }
            }
        }
    },
    /* CBUISpec_toThumbnailURL() */

};
/* CBBackgroundViewEditor */
