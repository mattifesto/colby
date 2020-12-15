"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBAjax,
    CBImage,
    CBModel,
    CBUI,
    CBUIImageChooser,
    CBUIPanel,
    CBUISpec,
    CBUISpecArrayEditor,
    CBUIStringEditor2,

    CBContainerView2Editor_addableClassNames,
*/



(function () {

    window.CBContainerView2Editor = {
        CBUISpecEditor_createEditorElement,
        CBUISpec_toDescription,
        CBUISpec_toThumbnailURL,
    };



    /**
     * @param object args
     *
     *      {
     *          spec: object,
     *          specChangedCallback: function,
     *      }
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        var section, item;
        var element = document.createElement("div");
        element.className = "CBContainerView2Editor";

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        let imageChooser = CBUIImageChooser.create();

        imageChooser.src = CBImage.toURL(
            spec.image,
            "rw960"
        );

        imageChooser.chosen = function (
            chooserArgs
        ) {
            CBAjax.call(
                "CBImages",
                "upload",
                {},
                chooserArgs.file
            ).then(
                function (imageModel) {
                    spec.image = imageModel;

                    imageChooser.src = CBImage.toURL(
                        imageModel,
                        "rw960"
                    );

                    specChangedCallback();
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(
                        error
                    );
                }
            );
        };

        imageChooser.removed = function () {
            spec.image = undefined;

            specChangedCallback();
        };

        item = CBUI.createSectionItem();
        item.appendChild(imageChooser.element);
        section.appendChild(item);

        element.appendChild(section);

        /* title */
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
                "title",
                "Title",
                specChangedCallback
            );

            sectionElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }
        /* title */


        /* subviews */
        {
            if (spec.subviews === undefined) {
                spec.subviews = [];
            }

            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: CBContainerView2Editor_addableClassNames,
                    specs: spec.subviews,
                    specsChangedCallback: specChangedCallback,
                }
            );

            editor.title = "Views";

            element.appendChild(editor.element);
            element.appendChild(CBUI.createHalfSpace());
        }


        /* CSSClassNames */
        {
            element.appendChild(
                CBUI.createSectionHeader(
                    {
                        paragraphs: [
                            `
                            Supported Class Names:
                            `,`
                            flow: Flow subviews from left to right and wrap into new
                            lines. Center each line of children. Example scenario:
                            displaying a collection of images.
                            `,`
                            hero1: Present the view as a full window view. The
                            minimum height is the window height. The background
                            image will always cover the entire view which will hide
                            some of the edges of the image depending on the shape of
                            the window.
                            `
                       ],
                    }
                )
            );

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


        /* localCSSTemplate */
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
                "localCSSTemplate",
                "Local CSS Template",
                specChangedCallback
            );

            sectionElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }
        /* localCSSTemplate */


        return element;
    }
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    function
    CBUISpec_toDescription(
        spec
    ) {
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
    }
    /* CBUISpec_toDescription() */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    function
    CBUISpec_toThumbnailURL(
        spec
    ) {
        if (spec.image) {
            return CBImage.toURL(
                spec.image,
                'rw320'
            );
        } else {
            if (Array.isArray(spec.subviews)) {
                for (let i = 0; i < spec.subviews.length; i++) {
                    let thumbnailURI = CBUISpec.specToThumbnailURL(
                        spec.subviews[i]
                    );

                    if (thumbnailURI) {
                        return thumbnailURI;
                    }
                }
            }
        }
    }
    /* CBUISpec_toThumbnailURL() */

})();
