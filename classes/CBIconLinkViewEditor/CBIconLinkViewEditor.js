"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBIconLinkViewEditor */
/* globals
    CBImage,
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIStringEditor,
    Colby,
*/

var CBIconLinkViewEditor = {

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
        var element = document.createElement("div");
        element.className = "CBIconLinkViewEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Text",
                    propertyName: "text",
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
                    labelText: "Text Color",
                    propertyName: "textColor",
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
                    labelText: "Alternative Text",
                    propertyName: "alternativeText",
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
                    labelText: "URL",
                    propertyName: "URL",
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
                    labelText: "Disable Rounded Corners",
                    propertyName: "disableRoundedCorners",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        /* image  */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        "Suggested Size: 320pt (640px) × 320pt (640px)",
                    ],
                    text: "Image"
                }
            )
        );

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = handleImageChosen;
        imageChooser.removed = handleImageRemoved;

        if (args.spec.image) {
            imageChooser.src = CBImage.toURL(args.spec.image, "rw960");
        }

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(imageChooser.element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function handleImageChosen(chooserArgs) {
            CBIconLinkViewEditor.promise = Colby.callAjaxFunction(
                "CBImages",
                "upload",
                {},
                chooserArgs.file
            ).then(
                function (imageModel) {
                    args.spec.image = imageModel;

                    args.specChangedCallback();

                    imageChooser.src = CBImage.toURL(imageModel, "rw960");
                }
            );
        }


        /**
         * @return undefined
         */
        function handleImageRemoved() {
            args.spec.image = undefined;

            args.specChangedCallback();
        }
    },
    /* createEditor() */


    /**
     * @param string? spec.text
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let text =
        CBModel.valueToString(spec, "text").trim();

        if (text !== "") {
            return text;
        }

        let alternativeText =
        CBModel.valueToString(spec, "alternativeText").trim();

        if (alternativeText !== "") {
            return alternativeText;
        }

        return undefined;
    },
    /* CBUISpec_toDescription() */
};
/* CBIconLinkViewEditor */
