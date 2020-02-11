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
        CBBackgroundViewEditor.prepareSpec(args.spec);

        var section, item;
        var element = CBUI.createElement("CBBackgroundViewEditor");

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Title",
                    propertyName: "title",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Background color",
                    propertyName: "color",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Repeat Horizontally",
                    propertyName: "imageShouldRepeatHorizontally",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Repeat Vertically",
                    propertyName: "imageShouldRepeatVertically",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Minimum View Height is Image Height",
                    propertyName: "minimumViewHeightIsImageHeight",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        {
            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: CBBackgroundViewEditor_addableClassNames,
                    specs: args.spec.children,
                    specsChangedCallback: args.specChangedCallback,
                }
            );

            editor.title = "Views";

            element.appendChild(editor.element);

            element.appendChild(
                CBUI.createHalfSpace()
            );
        }

        /* image */

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    text: "Background Image"
                }
            )
        );

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = createEditor_imageWasChosen;
        imageChooser.removed = createEditor_imageWasRemoved;

        updateImagePreview();

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(imageChooser.element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(
            CBUI.createHalfSpace()
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
                    args.spec.image = imageModel;
                    args.spec.imageHeight = imageModel.height;
                    args.spec.imageWidth = imageModel.width;
                    args.spec.imageURL = CBImage.toURL(
                        imageModel
                    );

                    updateImagePreview();

                    args.specChangedCallback();
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
            args.spec.image = undefined;
            args.spec.imageHeight = undefined;
            args.spec.imageWidth = undefined;
            args.spec.imageURL = undefined;

            updateImagePreview();

            args.specChangedCallback();
        }
        /* createEditor_imageWasRemoved() */



        /**
         * @return undefined
         */
        function updateImagePreview() {
            if (args.spec.imageURL) {
                if (args.spec.image) {
                    imageChooser.src = CBImage.toURL(
                        args.spec.image,
                        'rw960'
                    );
                } else {
                    imageChooser.src = args.spec.imageURL;
                }

                imageChooser.caption = (
                    args.spec.imageWidth +
                    "px × " +
                    args.spec.imageHeight +
                    "px"
                );
            } else {
                imageChooser.src = "";
                imageChooser.caption = "";
            }
        }
        /* updateImagePreview */

    },
    /* createEditor() */



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
