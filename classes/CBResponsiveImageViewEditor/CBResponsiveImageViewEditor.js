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
        var section, item, imageView, imageSizeView;
        var element = document.createElement("div");
        element.className = "CBResponsiveImageViewEditor";

        var styleChangedCallback = CBResponsiveImageViewEditor.handleStylesChanged.bind(undefined, {
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            status : {},
        });

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
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        /* large image section */
        element.appendChild(CBUI.createSectionHeader({ text : "Large Image" }));

        section = CBUI.createSection();

        /* image view */
        item = CBUI.createSectionItem();
        imageView = CBUIImageView.create({
            propertyName : "largeImage",
            spec : args.spec,
        });
        item.appendChild(imageView.element);
        section.appendChild(item);

        /* image size view */
        item = CBUI.createSectionItem();
        imageSizeView = CBUIImageSizeView.create({
            propertyName : "largeImage",
            spec : args.spec,
        });
        item.appendChild(imageSizeView.element);
        section.appendChild(item);

        /* image uploader */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIImageUploader.create({
            propertyName : "largeImage",
            spec : args.spec,
            specChangedCallback : styleChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* medium image section */
        element.appendChild(CBUI.createSectionHeader({ text : "Medium Image" }));

        section = CBUI.createSection();

        /* image view */
        item = CBUI.createSectionItem();
        imageView = CBUIImageView.create({
            propertyName : "mediumImage",
            spec : args.spec,
        });
        item.appendChild(imageView.element);
        section.appendChild(item);

        /* image size view */
        item = CBUI.createSectionItem();
        imageSizeView = CBUIImageSizeView.create({
            propertyName : "mediumImage",
            spec : args.spec,
        });
        item.appendChild(imageSizeView.element);
        section.appendChild(item);

        /* image uploader */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIImageUploader.create({
            propertyName : "mediumImage",
            spec : args.spec,
            specChangedCallback : styleChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* small image section */
        element.appendChild(CBUI.createSectionHeader({ text : "Small Image" }));

        section = CBUI.createSection();

        /* image view */
        item = CBUI.createSectionItem();
        imageView = CBUIImageView.create({
            propertyName : "smallImage",
            spec : args.spec,
        });
        item.appendChild(imageView.element);
        section.appendChild(item);

        /* image size view */
        item = CBUI.createSectionItem();
        imageSizeView = CBUIImageSizeView.create({
            propertyName : "smallImage",
            spec : args.spec,
        });
        item.appendChild(imageSizeView.element);
        section.appendChild(item);

        /* image uploader */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIImageUploader.create({
            propertyName : "smallImage",
            spec : args.spec,
            specChangedCallback : styleChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        return element;
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param object args.status
     *
     * @return undefined
     */
    handleStylesChanged : function (args) {
        if (args.status.waiting === true) {
            args.status.pending = true;
            return;
        } else if (args.status.pending === true) {
            args.status.pending = undefined;
        }

        var data = new FormData();
        data.append("specAsJSON", JSON.stringify(args.spec));

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, { xhr : xhr });
        xhr.onload = CBResponsiveImageViewEditor.handleUpdateStylesDidLoad.bind(undefined, args);
        xhr.open("POST", "/api/?class=CBResponsiveImageView&function=updateStyles");
        xhr.send(data);

        args.status.waiting = true;
        args.status.xhr = xhr;
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param object args.status
     *
     * @return undefined
     */
    handleUpdateStylesDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.status.xhr);
        args.status.waiting = undefined;
        args.status.xhr = undefined;

        if (response.wasSuccessful) {
            args.spec.themeID = response.themeID;
        } else {
            Colby.displayResponse(response);
        }

        if (args.status.pending) {
            CBResponsiveImageViewEditor.handleStylesChanged(args);
        } else {
            args.specChangedCallback();
        }
    },
};
