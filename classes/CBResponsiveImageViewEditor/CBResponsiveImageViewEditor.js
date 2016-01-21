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

        /* section */
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

        element.appendChild(CBUI.createHalfSpace());

        /* section */
        section = CBUI.createSection();

        /* title */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title",
            propertyName : "title",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* content width */
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Content Width",
            navigateCallback : args.navigateCallback,
            propertyName : "contentWidth2x",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            options : [
                { title : "None", value : undefined },
                { title : "320pt (640px)", value : 640 },
                { title : "480pt (960px)", value : 960 },
                { title : "640pt (1280px)", value : 1280 },
                { title : "800pt (1600px)", value : 1600 },
                { title : "960pt (1920px)", value : 1920 },
                { title : "1280pt (2560px)", value : 2560 },
                { title : "1600pt (3200px)", value : 3200 },
                { title : "1920pt (3840px)", value : 3840 },
                { title : "2560pt (5120px)", value : 5120 },
            ],
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
