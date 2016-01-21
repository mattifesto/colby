"use strict";

var CBResponsiveImageViewEditor = {

    /**
     * @param function args.navigateCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBResponsiveImageViewEditor";

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        var imageView = CBUIImageView.createElement({
            propertyName : "image",
            spec : args.spec,
        });

        item.appendChild(imageView.element);
        section.appendChild(item);

        item = CBUI.createSectionItem();

        var imageSizeView = CBUIImageSizeView.createElement({
            propertyName : "image",
            spec : args.spec,
        });

        item.appendChild(imageSizeView.element);
        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(CBUIImageUploader.createUploader({
            propertyName : "image",
            spec : args.spec,
            specChangedCallback : CBResponsiveImageViewEditor.handleImageChanged.bind(undefined, {
                propertyName : "image",
                spec : args.spec,
                specChangedCallback : args.specChangedCallback,
                updateImageCallback : imageView.updateImageCallback,
                updateImageSizeCallback : imageSizeView.updateImageSizeCallback,
            }),
        }).element);

        section.appendChild(item);
        element.appendChild(section);

        return element;
    },

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param function args.updateImageCallback
     * @param function args.updateImageSizeCallback
     *
     * @return undefined
     */
    handleImageChanged : function (args) {
        var image = args.spec[args.propertyName];
        args.updateImageCallback(image);
        args.updateImageSizeCallback(image);
        args.specChangedCallback();
    },
};
