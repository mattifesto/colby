"use strict";

var CBContainerViewEditor = {

    /**
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item, imageView, imageSizeView;
        var element = document.createElement("div");
        element.className = "CBContainerViewEditor";

        var styleChangedCallback = CBContainerViewEditor.handleStylesChanged.bind(undefined, {
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            status : {},
        });

        var HREFSectionItem = CBUI.createSectionItem();

        var tagNameChangedCallback = CBContainerViewEditor.handleTagNameChanged.bind(undefined, {
            HREFSectionItem : HREFSectionItem,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        tagNameChangedCallback();

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

        /* theme */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIThemeSelector.create({
            classNameForKind : "CBContainerView",
            labelText : "Theme",
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            propertyName : "themeID",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* tagName */
        item = CBUI.createSectionItem();
        var options = [
            { title : "Default", description: "The contents of this container have no specific purpose outside of being additional content.", value : undefined },
            { title : "Article", description: "The contents of this container represent a blog post or a syndicated article. This setting is not appropriate for regular pages such as the \"About\" page.", value : "article" },
            { title : "Section", description: "The contents of this container represent a section in a document.", value : "section" },
            { title : "Link", description : "This setting should only be used when this container's images must link to another page. Text links communicate much more clearly to the user and are highly preferred over image links and should be used whenever feasible. Adding subviews with links inside a container using this setting will cause severe layout issues by design and by all browsers.", value : "a" },
        ];
        item.appendChild(CBUISelector.create({
            labelText : "Type",
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            options : options,
            propertyName : "tagName",
            spec : args.spec,
            specChangedCallback : tagNameChangedCallback,
        }).element);
        section.appendChild(item);

        /* HREF */
        item = HREFSectionItem;
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "HREF",
            propertyName : "HREF",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* use image height */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Use Image Height",
            propertyName : "useImageHeight",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        /* subviews */
        element.appendChild(CBUI.createSectionHeader({ text : "Subviews" }));

        if (args.spec.subviews === undefined) { args.spec.subviews = []; }

        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.subviews,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBContainerViewAddableViews,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
        }));

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
            specChangedCallback : CBContainerViewEditor.handleImageChanged.bind(undefined, {
                callbacks : [
                    imageView.imageChangedCallback,
                    imageSizeView.imageChangedCallback,
                    styleChangedCallback,
                ],
            }),
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

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
            specChangedCallback : CBContainerViewEditor.handleImageChanged.bind(undefined, {
                callbacks : [
                    imageView.imageChangedCallback,
                    imageSizeView.imageChangedCallback,
                    styleChangedCallback,
                ],
            }),
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

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
            specChangedCallback : CBContainerViewEditor.handleImageChanged.bind(undefined, {
                callbacks : [
                    imageView.imageChangedCallback,
                    imageSizeView.imageChangedCallback,
                    styleChangedCallback,
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
    handleImageChanged : function (args) {
        args.callbacks.forEach(function (callback) { callback(); });
    },

    /**
     * @param Element args.HREFSectionItem
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    handleTagNameChanged : function (args) {
        if (args.spec.tagName === "a") {
            args.HREFSectionItem.style.display = "block";
        } else {
            args.HREFSectionItem.style.display = "none";
        }

        args.specChangedCallback.call();
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
        xhr.onload = CBContainerViewEditor.handleUpdateStylesDidLoad.bind(undefined, args);
        xhr.open("POST", "/api/?class=CBContainerView&function=updateStyles");
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
            args.spec.imageThemeID = response.imageThemeID;
        } else {
            Colby.displayResponse(response);
        }

        if (args.status.pending) {
            CBContainerViewEditor.handleStylesChanged(args);
        } else {
            args.specChangedCallback();
        }
    },

    /**
     * @param object spec
     * @param array? spec.children
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        if (spec.title) { return spec.title; }

        var description;
        var subviews = spec.subviews;

        if (Array.isArray(subviews)) {
            for (var i = 0; i < subviews.length && !description; i++) {
                description = CBArrayEditor.specToDescription(subviews[i]);
            }
        }

        return description;
    },
};
