"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBContainerViewEditor */
/* globals
    CBAjax,
    CBImage,
    CBModel,
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUISpec,
    CBUISpecArrayEditor,
    CBUIStringEditor2,

    CBContainerViewEditor_addableClassNames,
*/



var CBContainerViewEditor = {

    /* -- CBUISpec interfaces -- -- -- -- -- */



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



    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBContainerViewEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];
        let item;

        let HREFSectionItemElement = CBUI.createElement(
            "CBUISectionItem"
        );

        var tagNameChangedCallback =
        CBContainerViewEditor.handleTagNameChanged.bind(
            undefined,
            {
                HREFSectionItem: HREFSectionItemElement,
                spec: spec,
                specChangedCallback: specChangedCallback,
            }
        );

        tagNameChangedCallback();


        /* title */
        {
            let stringEditor = CBUIStringEditor2.create();

            stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
                spec,
                "title",
                "Title",
                specChangedCallback
            );

            sectionElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }

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
                    spec: spec,
                    specChangedCallback: tagNameChangedCallback,
                }
            ).element
        );

        sectionElement.appendChild(item);

        /* HREF */
        {
            let sectionItemElement = HREFSectionItemElement;

            sectionElement.appendChild(
                sectionItemElement
            );

            let stringEditor = CBUIStringEditor2.create();

            stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
                spec,
                "HREF",
                "URL",
                specChangedCallback
            );

            sectionItemElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }


        /* backgroundColor */
        {
            let stringEditor = CBUIStringEditor2.create();

            stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
                spec,
                "backgroundColor",
                "Background Color",
                specChangedCallback
            );

            sectionElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }
        /* backgroundColor */


        /* subviews */
        element.appendChild(
            CBUI.createHalfSpace()
        );

        if (spec.subviews === undefined) {
            spec.subviews = [];
        }

        {
            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: CBContainerViewEditor_addableClassNames,
                    specs: spec.subviews,
                    specsChangedCallback: specChangedCallback,
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

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];
            let stringEditor = CBUIStringEditor2.create();

            stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
                spec,
                "CSSClassNames",
                "CSS Class Names",
                specChangedCallback
            );

            sectionElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }
        /* CSSClassNames */


        /* local CSS template */
        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];
            let stringEditor = CBUIStringEditor2.create();

            stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
                spec,
                "stylesTemplate",
                "Styles Template",
                specChangedCallback
            );

            sectionElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }
        /* local CSS template */


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
            var sectionElement = CBUI.createSection();

            let imageChooser = CBUIImageChooser.create();
            imageChooser.chosen = createImageEditorElement_chosen;
            imageChooser.removed = createImageEditorElement_removed;
            imageChooser.src = imageToURL(spec[propertyName]);
            imageChooser.caption = imageToSize(spec[propertyName]);

            var item = CBUI.createSectionItem();

            item.appendChild(imageChooser.element);
            sectionElement.appendChild(item);

            return sectionElement;



            /* -- closures -- -- -- -- -- */



            /**
             * @return undefined
             */
            function createImageEditorElement_chosen() {
                CBAjax.call(
                    "CBImages",
                    "upload",
                    {},
                    imageChooser.file
                ).then(
                    function (imageModel) {
                        spec[propertyName] = imageModel;
                        imageChooser.src = imageToURL(imageModel);
                        imageChooser.caption = imageToSize(imageModel);

                        specChangedCallback();
                    }
                );
            }
            /* createImageEditorElement_chosen() */



            /**
             * @return undefined
             */
            function createImageEditorElement_removed() {
                spec[propertyName] = undefined;
                specChangedCallback();
            }
            /* createImageEditorElement_removed() */

        }
        /* createImageEditorElement() */

    },
    /* CBUISpecEditor_createEditorElement() */



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
