"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBContainerViewEditor */
/* globals
    CBImage,
    CBModel,
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUISpec,
    CBUISpecArrayEditor,
    CBUIStringEditor,
    Colby,

    CBContainerViewEditor_addableClassNames,
*/



var CBContainerViewEditor = {


    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let title = CBModel.valueToString(spec, "title").trim();

        if (title !== "") {
            return title;
        } else {
            if (Array.isArray(spec.subviews)) {
                for (let i = 0; i < spec.subviews.length; i++) {
                    let description = CBUISpec.specToDescription(
                        spec.subviews[i]
                    );

                    if (description) {
                        return description;
                    }
                }
            }
        }
    },
    /* CBUISpec_toDescription() */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toThumbnailURL: function (spec) {
        let image = spec.smallImage || spec.mediumImage || spec.largeImage;

        if (image) {
            return CBImage.toURL(
                image,
                'rw320'
            );
        } else {
            if (Array.isArray(spec.subviews)) {
                for (let i = 0; i < spec.subviews.length; i++) {
                    let thumbnailURL = CBUISpec.specToThumbnailURL(
                        spec.subviews[i]
                    );

                    if (thumbnailURL) {
                        return thumbnailURL;
                    }
                }
            }
        }
    },
    /* CBUISpec_toThumbnailURL() */



    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBContainerViewEditor";

        var HREFSectionItem = CBUI.createSectionItem();

        var tagNameChangedCallback =
        CBContainerViewEditor.handleTagNameChanged.bind(
            undefined,
            {
                HREFSectionItem: HREFSectionItem,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }
        );

        tagNameChangedCallback();

        element.appendChild(
            CBUI.createHalfSpace()
        );

        /* section */
        section = CBUI.createSection();

        /* title */
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Title",
                    propertyName: "title",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        /* tagName */
        item = CBUI.createSectionItem();

        var options = [
            {
                title: "Default",
                description: `

                    The contents of this container have no specific purpose
                    outside of being additional content.

                `,
                value: undefined
            },
            {
                title: "Article",
                description: `

                    The contents of this container represent a blog post or a
                    syndicated article. This setting is not appropriate for
                    regular pages such as the \"About\" page.

                `,
                value: "article"
            },
            {
                title: "Section",
                description: `

                    The contents of this container represent a section in a
                    document.

                `,
                value: "section"
            },
            {
                title: "Link",
                description: `

                    This setting should only be used when this container's
                    images must link to another page. Text links communicate
                    much more clearly to the user and are highly preferred over
                    image links and should be used whenever feasible. Adding
                    subviews with links inside a container using this setting
                    will cause severe layout issues by design and by all
                    browsers.

                `,
                value: "a"
            },
        ];

        item.appendChild(
            CBUISelector.create(
                {
                    labelText: "Type",
                    options: options,
                    propertyName: "tagName",
                    spec: args.spec,
                    specChangedCallback: tagNameChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        /* HREF */
        item = HREFSectionItem;

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "URL",
                    propertyName: "HREF",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        /* backgroundColor */
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Background Color",
                    propertyName: "backgroundColor",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        /* subviews */
        element.appendChild(
            CBUI.createHalfSpace()
        );

        if (args.spec.subviews === undefined) {
            args.spec.subviews = [];
        }

        {
            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: CBContainerViewEditor_addableClassNames,
                    specs: args.spec.subviews,
                    specsChangedCallback: args.specChangedCallback,
                }
            );

            editor.title = "Views";

            element.appendChild(editor.element);
        }


        /* large image */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        "Maximum Width: 2560pt (5120px)",
                        "Focus Width: 1068pt (2136px)",
                    ],
                    text: "Large Image"
                }
            )
        );

        element.appendChild(createImageEditorElement("largeImage"));

        /* medium image */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        "Maximum Width: 1068pt (2136px)",
                        "Focus Width: 736pt (1472px)",
                    ],
                    text: "Medium Image"
                }
            )
        );

        element.appendChild(createImageEditorElement("mediumImage"));

        /* small image */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        "Maximum Width: 736pt (1472px)",
                        "Focus Width: 320pt (640px)",
                    ],
                    text: "Small Image"
                }
            )
        );

        element.appendChild(createImageEditorElement("smallImage"));

        /* CSSClassNames */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        `

                            Supported Class Names:

                        `,
                        `

                            flow: Flow subviews from left to right and wrap into
                            new lines. Center each line of children. Example
                            scenario: displaying a collection of images.

                        `,
                        `

                            noMinHeight: Don't use the height of the background
                            images or any other minimum height specified as the
                            minimum height for the view.

                        `,
                   ],
                }
            )
        );

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "CSS Class Names",
                    propertyName: "CSSClassNames",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        /* local CSS template */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Styles Template",
                    propertyName: "stylesTemplate",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;



        /* -- closures -- -- -- -- -- */



        /**
         * @return string
         */
        function imageToSize(image) {
            if (image && image.width && image.height) {
                return (image.width / 2) + "pt × " + (image.height / 2) + "pt";
            } else {
                return "";
            }
        }



        /**
         * @return string
         */
        function imageToURL(image) {
            return CBImage.toURL(
                image,
                "rw960"
            );
        }



        /**
         * @return Element
         */
        function createImageEditorElement(propertyName) {
            var section = CBUI.createSection();

            let imageChooser = CBUIImageChooser.create();
            imageChooser.chosen = createImageEditorElement_chosen;
            imageChooser.removed = createImageEditorElement_removed;
            imageChooser.src = imageToURL(args.spec[propertyName]);
            imageChooser.caption = imageToSize(args.spec[propertyName]);

            var item = CBUI.createSectionItem();

            item.appendChild(imageChooser.element);
            section.appendChild(item);

            return section;



            /* -- closures -- -- -- -- -- */



            /**
             * @return undefined
             */
            function createImageEditorElement_chosen() {
                Colby.callAjaxFunction(
                    "CBImages",
                    "upload",
                    {},
                    imageChooser.file
                ).then(
                    function (imageModel) {
                        args.spec[propertyName] = imageModel;
                        imageChooser.src = imageToURL(imageModel);
                        imageChooser.caption = imageToSize(imageModel);

                        args.specChangedCallback();
                    }
                );
            }
            /* createImageEditorElement_chosen() */



            /**
             * @return undefined
             */
            function createImageEditorElement_removed() {
                args.spec[propertyName] = undefined;
                args.specChangedCallback();
            }
            /* createImageEditorElement_removed() */

        }
        /* createImageEditorElement() */

    },
    /* createEditor() */



    /**
     * @param Element args.HREFSectionItem
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    handleTagNameChanged: function (args) {
        if (args.spec.tagName === "a") {
            args.HREFSectionItem.style.display = "block";
        } else {
            args.HREFSectionItem.style.display = "none";
        }

        args.specChangedCallback.call();
    },
    /* handleTagNameChanged() */

};
/* CBContainerViewEditor */
