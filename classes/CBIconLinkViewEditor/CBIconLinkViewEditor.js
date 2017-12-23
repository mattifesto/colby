"use strict";
/* jshint strict: global */
/* exported CBIconLinkViewEditor */
/* globals
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIStringEditor,
    Colby */

var CBIconLinkViewEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBIconLinkViewEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Text",
            propertyName : "text",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Text Color",
            propertyName : "textColor",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Alternative Text",
            propertyName : "alternativeText",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "URL",
            propertyName : "URL",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Disable Rounded Corners",
            propertyName : "disableRoundedCorners",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* image  */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            paragraphs : [
                "Suggested Size: 320pt (640px) × 320pt (640px)",
            ],
            text : "Image"
        }));

        var chooser = CBUIImageChooser.createFullSizedChooser({
            imageChosenCallback : handleImageChosen,
            imageRemovedCallback : handleImageRemoved,
        });

        if (args.spec.image) {
            chooser.setImageURLCallback(Colby.imageToURL(args.spec.image, "rw960"));
        }

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;

        /**
         *
         */
        function handleImageChosen(chooserArgs) {
            var ajaxURI = "/api/?class=CBImages&function=upload";
            var formData = new FormData();
            formData.append("image", chooserArgs.file);

            CBIconLinkViewEditor.promise = Colby.fetchAjaxResponse(ajaxURI, formData)
                .then(handleImageUploaded);

            function handleImageUploaded(response) {
                args.spec.image = response.image;
                args.specChangedCallback();
                chooserArgs.setImageURLCallback(Colby.imageToURL(args.spec.image, "rw960"));
            }
        }

        /**
         *
         */
        function handleImageRemoved(chooserArgs) {
            args.spec.image = undefined;
            args.specChangedCallback();
        }
    },

    /**
     * @param [function] args.callbacks
     *
     * @return undefined
     */
    imageChanged : function (args) {
        args.callbacks.forEach(function (callback) {
            callback.call();
        });
    },

    /**
     * @param string? spec.text
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        if (typeof spec.text === "string" && spec.text.trim()) {
            return spec.text;
        } else if (typeof spec.alternativeText === "string" && spec.alternativeText.trim()) {
            return spec.alternativeText;
        } else {
            return undefined;
        }
    },
};
