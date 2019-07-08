"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBImageLinkViewEditor */
/* global
    CBImage,
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageUploader,
    CBUIImageURLView,
    CBUIStringEditor,
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
        var section, item;

        let spec = CBModel.valueAsModel(args, "spec");

        if (spec === undefined) {
            throw TypeError("spec");
        }

        var element = document.createElement("div");
        element.className = "CBImageLinkViewEditor";
        var dimensions = document.createElement("div");
        dimensions.className = "dimensions";

        /* upgrade spec */
        if (spec.density !== undefined) {
            spec.retina = (spec.density === "2x");
            spec.density = undefined;
        }

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        /* image view */
        item = CBUI.createSectionItem();

        var imageView = CBUIImageURLView.create(
            {
                propertyName: "URL",
                spec: spec,
            }
        );

        item.appendChild(imageView.element);
        section.appendChild(item);

        /* dimensions */
        item = CBUI.createSectionItem();
        item.appendChild(dimensions);
        section.appendChild(item);

        /* image uploader */

        var specWithImage = {};

        var updateDimensionsCallback = CBImageLinkViewEditor.updateDimensions.bind(undefined, {
            dimensionsElement: dimensions,
            spec: spec,
        });

        updateDimensionsCallback();


        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIImageUploader.create(
                {
                    propertyName: "image",
                    spec: specWithImage,
                    specChangedCallback: function () {
                        createEditor_handleImageUploaded();
                    },
                }
            ).element
        );

        section.appendChild(item);

        /* retina */
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Retina",
                    propertyName: "retina",
                    spec: spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        /* alternative text */
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Alternative Text",
                    propertyName: "alt",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        /* link href */
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Link HREF",
                    propertyName: "HREF",
                    spec: spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

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
        function createEditor_handleImageUploaded() {
            let image = specWithImage.image;

            spec.height = image.height;
            spec.width = image.width;
            spec.URL = CBImage.toURL(image);

            imageView.imageChangedCallback();
            updateDimensionsCallback();
            args.specChangedCallback();
        }
        /* createEditor_handleImageUploaded() */
    },
    /* createEditor() */


    /**
     * @param object spec
     * @param string? spec.alt
     *
     * @return string|undefined
     */
    specToDescription: function (spec) {
        return spec.alt;
    },


    /**
     * @param object spec
     *
     * @return string
     */
    specToDimensionsText: function (spec) {
        if (spec.URL === undefined) {
            return "no image";
        } else {
            var width = (spec.width/2) + "pt (" + spec.width + "px)";
            var height = (spec.height/2) + "pt (" + spec.height + "px)";
            return width + " × " + height;
        }
    },


    /**
     * @param Element args.dimensionsElement
     * @param object args.spec
     *
     * @return undefined
     */
    updateDimensions: function (args) {
        args.dimensionsElement.textContent = CBImageLinkViewEditor.specToDimensionsText(args.spec);
    },
};
/* CBImageLinkViewEditor */
