"use strict";
/* jshint strict: global */
/* exported CBBackgroundViewEditor */
/* global
    CBUI,
    CBArrayEditor,
    CBBackgroundViewAddableViews,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUISpec,
    CBUIStringEditor,
    Colby */

var CBBackgroundViewEditor = {

    /**
     * @param function args.navigateToItemCallback
     * @param Object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function(args) {
        CBBackgroundViewEditor.prepareSpec(args.spec);

        var section, item;
        var element = document.createElement("div");
        element.className = "CBBackgroundViewEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title",
            propertyName : "title",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Background color",
            propertyName : "color",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Repeat Horizontally",
            propertyName : "imageShouldRepeatHorizontally",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Repeat Vertically",
            propertyName : "imageShouldRepeatVertically",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Minimum View Height is Image Height",
            propertyName : "minimumViewHeightIsImageHeight",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({ text : "Subviews" }));
        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.children,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBBackgroundViewAddableViews,
            navigateToItemCallback : args.navigateToItemCallback,
        }));

        /* image */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({
            text: "Background Image"
        }));

        var chooser = CBUIImageChooser.createFullSizedChooser({
            imageChosenCallback: function (chooserArgs) {
                var formData = new FormData();
                formData.append("image", chooserArgs.file);

                Colby.fetchAjaxResponse("/api/?class=CBImages&function=upload", formData)
                    .then(function (response) {
                        args.spec.image = response.image;
                        args.spec.imageHeight = response.image.height;
                        args.spec.imageWidth = response.image.width;
                        args.spec.imageURL = Colby.imageToURL(response.image);

                        updateImagePreview();

                        args.specChangedCallback();
                    })
                    .catch(Colby.displayAndReportError);
            },
            imageRemovedCallback: function () {
                args.spec.image = undefined;
                args.spec.imageHeight = undefined;
                args.spec.imageWidth = undefined;
                args.spec.imageURL = undefined;

                updateImagePreview();

                args.specChangedCallback();
            },
        });

        function updateImagePreview() {
            if (args.spec.imageURL) {
                if (args.spec.image) {
                    chooser.setImageURLCallback(Colby.imageToURL(args.spec.image, 'rw960'));
                } else {
                    chooser.setImageURLCallback(args.spec.imageURL);
                }

                chooser.setCaptionCallback(args.spec.imageWidth + "px × " + args.spec.imageHeight + "px");
            } else {
                chooser.setImageURLCallback();
                chooser.setCaptionCallback("");
            }
        }

        updateImagePreview();

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;
    },

    /**
     * @return undefined
     */
    prepareSpec: function (spec) {
        if (!spec.children) {
            spec.children = [];
        }

        if (spec.minimumViewHeightIsImageHeight === undefined) {
            spec.minimumViewHeightIsImageHeight = true;
        }
    },

    /**
     * @param object spec
     * @param array? spec.children
     *
     * @return string|undefined
     */
    specToDescription: function (spec) {
        if (spec.title) { return spec.title; }

        var description;
        var children = spec.children;

        if (Array.isArray(children)) {
            for (var i = 0; i < children.length && !description; i++) {
                description = CBUISpec.specToDescription(children[i]);
            }
        }

        return description;
    },

    /**
     * @param object spec
     *
     * @return string|undefined
     */
    specToThumbnailURI: function (spec) {
        if (spec.image) {
            return Colby.imageToURL(spec.image, 'rw320');
        } else if (spec.imageURL) {
            return spec.imageURL;
        } else {
            return undefined;
        }
    },
};
