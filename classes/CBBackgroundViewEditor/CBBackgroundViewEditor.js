"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBBackgroundViewEditor */
/* global
    CBUI,
    CBBackgroundViewEditor_addableClassNames,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUISpec,
    CBUISpecArrayEditor,
    CBUIStringEditor,
    Colby */

var CBBackgroundViewEditor = {

    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        if (typeof spec.title === "string" && spec.title.trim() !== "") {
            return spec.title.trim();
        } else {
            if (Array.isArray(spec.children)) {
                for (let i = 0; i < spec.children.length; i++) {
                    let description = CBUISpec.specToDescription(spec.children[i]);

                    if (description) {
                        return description;
                    }
                }
            }
        }
    },

    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toThumbnailURI: function (spec) {
        if (spec.image) {
            return Colby.imageToURL(spec.image, 'rw320');
        } else if (spec.imageURL) {
            return spec.imageURL;
        } else {
            if (Array.isArray(spec.children)) {
                for (let i = 0; i < spec.children.length; i++) {
                    let thumbnailURI = CBUISpec.specToThumbnailURI(spec.children[i]);

                    if (thumbnailURI) {
                        return thumbnailURI;
                    }
                }
            }
        }
    },

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

        {
            let editor = CBUISpecArrayEditor.create({
                addableClassNames: CBBackgroundViewEditor_addableClassNames,
                navigateToItemCallback: args.navigateToItemCallback,
                specs: args.spec.children,
                specsChangedCallback: args.specChangedCallback,
            });

            editor.title = "Views";

            element.appendChild(editor.element);
            element.appendChild(CBUI.createHalfSpace());
        }

        /* image */

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
};
