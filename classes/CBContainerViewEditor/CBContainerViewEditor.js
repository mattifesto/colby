"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* globals
    CBArrayEditor,
    CBContainerViewAddableViews,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUISelector,
    CBUISpec,
    CBUIStringEditor,
    CBUIThemeSelector,
    Colby */

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
        var section, item;
        var element = document.createElement("div");
        element.className = "CBContainerViewEditor";

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

        /* backgroundPositionY */
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Background Position",
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            propertyName : "backgroundPositionY",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            options : [
                { title : "Default (Top)", value : undefined },
                { title : "Center", value : "center" },
                { title : "Bottom", value : "bottom" },
            ],
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
            labelText : "URL",
            propertyName : "HREF",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* backgroundColor */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Background Color",
            propertyName : "backgroundColor",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* backgroundImage */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Background Gradient",
            propertyName : "backgroundImage",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* useImageHeight */
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

        /**
         * image properties
         */

        function imageToSize(image) {
            if (image && image.width && image.height) {
                return (image.width / 2) + "pt × " + (image.height / 2) + "pt";
            } else {
                return "";
            }
        }

        function imageToURL(image) {
            return Colby.imageToURL(image, "rw960");
        }

        function createImageEditorElement(propertyName) {
            var section = CBUI.createSection();
            var chooser = CBUIImageChooser.createFullSizedChooser({
                imageChosenCallback : function (imageChosenArgs) {
                    var ajaxURI = "/api/?class=CBImages&function=upload";
                    var formData = new FormData();
                    formData.append("image", imageChosenArgs.file);

                    CBContainerViewEditor.promise = Colby.fetchAjaxResponse(ajaxURI, formData)
                        .then(handleImageUploaded);

                    function handleImageUploaded(response) {
                        args.spec[propertyName] = response.image;
                        args.specChangedCallback();
                        imageChosenArgs.setImageURLCallback(imageToURL(response.image));
                        imageChosenArgs.setCaptionCallback(imageToSize(response.image));
                    }
                },
                imageRemovedCallback : function () {
                    args.spec[propertyName] = undefined;
                    args.specChangedCallback();
                },
            });

            chooser.setImageURLCallback(imageToURL(args.spec[propertyName]));
            chooser.setCaptionCallback(imageToSize(args.spec[propertyName]));

            var item = CBUI.createSectionItem();
            item.appendChild(chooser.element);
            section.appendChild(item);

            return section;
        }

        /* large image */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            paragraphs : [
                "Maximum Width: 2560pt (5120px)",
                "Focus Width: 1068pt (2136px)",
            ],
            text : "Large Image"
        }));
        element.appendChild(createImageEditorElement("largeImage"));

        /* medium image */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            paragraphs : [
                "Maximum Width: 1068pt (2136px)",
                "Focus Width: 736pt (1472px)",
            ],
            text : "Medium Image"
        }));
        element.appendChild(createImageEditorElement("mediumImage"));

        /* small image */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            paragraphs : [
                "Maximum Width: 736pt (1472px)",
                "Focus Width: 320pt (640px)",
            ],
            text : "Small Image"
        }));
        element.appendChild(createImageEditorElement("smallImage"));

        /* CSSClassNames */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({
            paragraphs: [
                `
                Supported Class Names:
                `,`
                flow: Flow subviews from left to right and wrap into new lines.
                Center each line of children. Example scenario: displaying a
                collection of images.
                `,
           ],
        }));

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "CSS Class Names",
            propertyName : "CSSClassNames",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* local CSS template */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Styles Template",
            propertyName : "stylesTemplate",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        return element;
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
                description = CBUISpec.specToDescription(subviews[i]);
            }
        }

        return description;
    },
};
