"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBImageLinkViewEditor */
/* global
    CBImage,
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIStringEditor,
    CBUIPanel,
    Colby,
*/



var CBImageLinkViewEditor = {

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return  Element
     */
    createEditor: function (args) {
        let spec = CBModel.valueAsModel(args, "spec");

        if (spec === undefined) {
            throw TypeError("spec");
        }

        let specChangedCallback = CBModel.valueAsFunction(
            args,
            "specChangedCallback"
        );

        if (specChangedCallback === undefined) {
            throw TypeError("specChangedCallback");
        }

        var element = CBUI.createElement("CBImageLinkViewEditor");

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = createEditor_handleImageChosen;
        imageChooser.removed = createEditor_handleImageRemoved;

        /* upgrade spec */
        if (spec.density !== undefined) {
            spec.retina = (spec.density === "2x");
            spec.density = undefined;
        }


        let sectionElement = CBUI.createElement("CBUI_section");

        {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            sectionContainerElement.appendChild(sectionElement);

            element.appendChild(sectionContainerElement);
        }


        /* -- image chooser -- -- -- -- -- */

        {
            let sectionItemElement = CBUI.createElement("CBUI_sectionItem");

            sectionElement.appendChild(sectionItemElement);

            sectionItemElement.appendChild(imageChooser.element);
        }


        /* -- dimensions text element -- -- -- -- -- */

        let dimensionsTextElement = CBUI.createElement(
            "CBUI_textColor2 CBUI_textAlign_center"
        );

        {
            let sectionItemElement = CBUI.createElement(
                "CBUI_container_topAndBottom"
            );

            sectionElement.appendChild(sectionItemElement);

            sectionItemElement.appendChild(dimensionsTextElement);
        }


        /* -- retina -- -- -- -- -- */

        {
            let sectionItemElement = CBUI.createElement("CBUI_sectionItem");

            sectionElement.appendChild(sectionItemElement);

            sectionItemElement.appendChild(
                CBUIBooleanEditor.create(
                    {
                        labelText: "Retina",
                        propertyName: "retina",
                        spec: spec,
                        specChangedCallback: specChangedCallback,
                    }
                ).element
            );
        }


        /* -- alternative text -- -- -- -- -- */

        {
            let sectionItemElement = CBUI.createElement("CBUI_sectionItem");

            sectionElement.appendChild(sectionItemElement);

            sectionItemElement.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "Alternative Text",
                        propertyName: "alt",
                        spec: spec,
                        specChangedCallback: specChangedCallback,
                    }
                ).element
            );
        }


        /* -- URL -- -- -- -- -- */

        {
            let sectionItemElement = CBUI.createElement("CBUI_sectionItem");

            sectionElement.appendChild(sectionItemElement);

            sectionItemElement.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "URL",
                        propertyName: "HREF",
                        spec: spec,
                        specChangedCallback: specChangedCallback,
                    }
                ).element
            );
        }

        createEditor_updateDimensions();

        /* set thumbnail */

        if (spec.URL) {
            imageChooser.src = spec.URL;
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
                    spec.image = imageModel;
                    spec.height = imageModel.height;
                    spec.width = imageModel.width;
                    spec.URL = CBImage.toURL(imageModel);

                    createEditor_updateDimensions();
                    specChangedCallback();

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
                    CBUIPanel.displayAndReportError(error);
                }
            );
        }
        /* createEditor_handleImageChosen() */


        /**
         * @return undefined
         */
        function createEditor_handleImageRemoved() {
            spec.image = undefined;
            spec.height = undefined;
            spec.width = undefined;
            spec.URL = undefined;

            createEditor_updateDimensions();
            specChangedCallback();
        }
        /* createEditor_handleImageRemoved() */


        /**
         * @return undefined
         */
        function createEditor_updateDimensions() {
            if (spec.URL === undefined) {
                dimensionsTextElement.textContent = "no image";
            } else {
                var width = (spec.width/2) + "pt (" + spec.width + "px)";
                var height = (spec.height/2) + "pt (" + spec.height + "px)";
                dimensionsTextElement.textContent = width + " × " + height;
            }
        }
        /* createEditor_updateDimensions() */
    },
    /* createEditor() */


    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        return CBModel.valueToString(spec, "alt").trim() || undefined;
    },
    /* CBUISpec_toDescription() */
};
/* CBImageLinkViewEditor */
