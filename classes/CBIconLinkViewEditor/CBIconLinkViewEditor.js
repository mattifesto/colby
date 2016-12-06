"use strict"; /* jshint strict: global */
/* globals CBUI, CBUIBooleanEditor, CBUIImageView, CBUIImageSizeView,
           CBUIImageUploader, CBUIStringEditor */

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
        element.appendChild(CBUI.createHalfSpace());

        /* image section */
        element.appendChild(CBUI.createSectionHeader({
            paragraphs : [
                "Suggested Size: 320pt (640px) × 320pt (640px)",
            ],
            text : "Image"
        }));

        section = CBUI.createSection();

        /* image view */
        item = CBUI.createSectionItem();
        var imageView = CBUIImageView.create({
            propertyName : "image",
            spec : args.spec,
        });
        item.appendChild(imageView.element);
        section.appendChild(item);

        /* image size view */
        item = CBUI.createSectionItem();
        var imageSizeView = CBUIImageSizeView.create({
            propertyName : "image",
            spec : args.spec,
        });
        item.appendChild(imageSizeView.element);
        section.appendChild(item);

        /* image uploader */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIImageUploader.create({
            propertyName : "image",
            spec : args.spec,
            specChangedCallback : CBIconLinkViewEditor.imageChanged.bind(undefined, {
                callbacks : [
                    imageView.imageChangedCallback,
                    imageSizeView.imageChangedCallback,
                    args.specChangedCallback,
                ],
            }),
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        return element;
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
