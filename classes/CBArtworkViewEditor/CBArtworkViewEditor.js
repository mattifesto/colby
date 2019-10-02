"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBArtworkViewEditor */
/* globals
    CBErrorHandler,
    CBImage,
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUIStringEditor,
    Colby,
*/


var CBArtworkViewEditor = {

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
    createEditor: function (args) {
        var section, item;
        var element = CBUI.createElement("CBArtworkViewEditor");

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = createEditor_handleImageChosen;
        imageChooser.removed = createEditor_handleImageRemoved;

        item = CBUI.createSectionItem();

        item.appendChild(imageChooser.element);
        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Alternative Text",
                    propertyName: "alternativeText",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);


        /* captionAsCBMessage, captionAsMarkdown */
        {
            let propertyName;
            let title;

            let captionAsMarkdown = CBModel.valueToString(
                args.spec,
                "captionAsMarkdown"
            );

            if (captionAsMarkdown.trim() === "") {
                title = "Caption (cbmessage)";
                propertyName = "captionAsCBMessage";
            } else {
                title = "Caption (markdown)";
                propertyName = "captionAsMarkdown";
            }

            let stringEditor = CBUIStringEditor.createEditor(
                {
                    labelText: title,
                    propertyName: propertyName,
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            );

            section.appendChild(stringEditor.element);
        }
        /* captionAsCBMessage, captionAsMarkdown */


        item = CBUI.createSectionItem();
        item.appendChild(
            CBUISelector.create(
                {
                    labelText: "Maximum Display Width",
                    options: [
                        {
                            title: "160 CSS pixels",
                            value: "rw320",
                        },
                        {
                            title: "320 CSS pixels",
                            value: "rw640",
                        },
                        {
                            title: "480 CSS pixels",
                            value: "rw960",
                        },
                        {
                            title: "640 CSS pixels",
                            value: "rw1280",
                        },
                        {
                            title: "800 CSS pixels (default)",
                        },
                        {
                            title: "960 CSS pixels",
                            value: "rw1920",
                        },
                        {
                            title: "1280 CSS pixels",
                            value: "rw2560",
                        },
                        {
                            title: "Image Width",
                            description:
                            "The maximum width in CSS pixels is half the count of" +
                            " horizontal pixels of the uploaded image.",
                            value: "original",
                        },
                        {
                            title: "Page Width",
                            description:
                            "The uploaded image will always use the full width of" +
                            " the page regardless of its size.",
                            value: "page",
                        },
                    ],
                    propertyName: "size",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        element.appendChild(section);

        /* CSSClassNames */

        element.appendChild(
            CBUI.createHalfSpace()
        );

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        `
                        Supported Class Names:
                        `,`
                        hideSocial: This will hide links to share the image via
                        social networks.
                        `,`
                        left: Align the caption text to the left.
                        `,`
                        right: Align the caption text to the right.
                        `
                   ],
                }
            )
        );

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "CSS Class Names",
                    propertyName: "CSSClassNames",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(
            CBUI.createHalfSpace()
        );

        /* set thumbnail */

        if (args.spec.image) {
            imageChooser.src = CBImage.toURL(args.spec.image, "rw960");
        }

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @param object chooseArgs
         *
         * @return undefined
         */
        function createEditor_handleImageChosen() {
            Colby.callAjaxFunction(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            ).then(
                function (imageModel) {
                    args.spec.image = imageModel;

                    args.specChangedCallback();

                    imageChooser.src = CBImage.toURL(imageModel, "rw960");

                    let suggestThumbnailImage = CBModel.valueAsFunction(
                        window.CBViewPageEditor,
                        "suggestThumbnailImage"
                    );

                    if (suggestThumbnailImage) {
                        suggestThumbnailImage(imageModel);
                    }
                }
            ).catch(
                function (error) {
                    CBErrorHandler.displayAndReport(error);
                }
            );
        }
        /* createEditor_handleImageChosen() */



        /**
         * @return undefined
         */
        function createEditor_handleImageRemoved() {
            args.spec.image = undefined;
            args.specChangedCallback();
        }
        /* createEditor_handleImageRemoved() */
    },
    /* createEditor() */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let alternativeText = CBModel.valueToString(
            spec,
            "alternativeText"
        ).trim();

        if (alternativeText !== "") {
            return alternativeText;
        }

        let captionAsMarkdown = CBModel.valueToString(
            spec,
            "captionAsMarkdown"
        ).trim();

        if (captionAsMarkdown !== "") {
            return captionAsMarkdown;
        }

        let cbmessage = CBMessageMarkup.messageToText(
            CBModel.valueToString(
                spec,
                "captionAsCBMessage"
            )
        ).trim();

        if (cbmessage === "") {
            return undefined;
        } else {
            return cbmessage;
        }
    },
    /* CBUISpec_toDescription() */

};
/* CBArtworkViewEditor */
