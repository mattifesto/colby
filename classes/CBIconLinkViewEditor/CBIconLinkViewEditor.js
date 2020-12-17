"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBIconLinkViewEditor */
/* globals
    CBAjax,
    CBImage,
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIStringEditor2,
*/



var CBIconLinkViewEditor = {

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
        var section, item;
        var element = document.createElement("div");
        element.className = "CBIconLinkViewEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        section.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "text",
                "Text",
                specChangedCallback
            )
        );

        section.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "textColor",
                "Text Color",
                specChangedCallback
            )
        );

        section.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "alternativeText",
                "Alternative Text",
                specChangedCallback
            )
        );

        section.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "URL",
                "URL",
                specChangedCallback
            )
        );

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
            CBIconLinkViewEditor.promise = CBAjax.call(
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
    /* CBUISpecEditor_createEditorElement() */



    /* -- CBUISpec interfaces -- */



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
