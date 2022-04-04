/* globals
    CBAjax,
    CBConvert,
    CBImage,
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIImageChooser,
    CBUIPanel,
    CBUISelector,
    CBUIStringEditor2,
*/


(function () {
    "use strict";

    window.CBArtworkViewEditor =
    {
        CBUISpecEditor_createEditorElement,
        CBUISpec_toDescription,
    };



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement(
        args
    ) // -> Element
    {
        let spec =
        args.spec;

        let specChangedCallback =
        args.specChangedCallback;

        let sectionElement, item;

        let element =
        CBUI.createElement(
            "CBArtworkViewEditor"
        );

        element.appendChild(
            CBUI.createHalfSpace()
        );

        sectionElement =
        CBUI.createSection();

        element.appendChild(
            sectionElement
        );

        let imageChooser =
        CBUIImageChooser.create();

        imageChooser.chosen =
        createEditor_handleImageChosen;

        imageChooser.removed =
        createEditor_handleImageRemoved;

        item =
        CBUI.createSectionItem();

        item.appendChild(
            imageChooser.element
        );

        sectionElement.appendChild(
            item
        );


        /* alternative text */

        sectionElement.appendChild(
            createAlternativeTextEditorElement(
                spec,
                specChangedCallback
            )
        );


        /* captionAsCBMessage, captionAsMarkdown */

        sectionElement.appendChild(
            createCaptionEditorElement(
                spec,
                specChangedCallback
            )
        );


        /* maximum display width */

        item =
        CBUI.createSectionItem();

        item.appendChild(
            CBUISelector.create(
                {
                    labelText:
                    "Maximum Display Width",

                    options:
                    [
                        {
                            title:
                            "160 CSS pixels",

                            value:
                            "rw320",
                        },
                        {
                            title:
                            "320 CSS pixels",

                            value:
                            "rw640",
                        },
                        {
                            title:
                            "480 CSS pixels",

                            value:
                            "rw960",
                        },
                        {
                            title:
                            "640 CSS pixels",

                            value:
                            "rw1280",
                        },
                        {
                            title:
                            "800 CSS pixels (default)",
                        },
                        {
                            title:
                            "960 CSS pixels",

                            value:
                            "rw1920",
                        },
                        {
                            title:
                            "1280 CSS pixels",

                            value:
                            "rw2560",
                        },
                        {
                            title:
                            "Image Width",

                            description:
                            CBConvert.stringToCleanLine(`

                                The maximum width in CSS pixels is half the
                                count of horizontal pixels of the uploaded
                                image.

                            `),

                            value:
                            "original",
                        },
                        {
                            title:
                            "Page Width",

                            description:
                            CBConvert.stringToCleanLine(`

                                The uploaded image will always use the full
                                width of the page regardless of its size.

                            `),

                            value:
                            "page",
                        },
                    ],

                    propertyName:
                    "size",

                    spec:
                    args.spec,

                    specChangedCallback:
                    args.specChangedCallback,
                }
            ).element
        );

        sectionElement.appendChild(
            item
        );

        /* render image only */

        let elements =
        CBUI.createElementTree(
            "CBUI_sectionItem CBArtworkViewEditor_renderImageOnly",
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "label"
        );

        sectionElement.appendChild(
            elements[0]
        );

        elements[2].textContent =
        "Render Image Only";

        let renderImageOnlyEditor =
        CBUIBooleanSwitchPart.create();

        renderImageOnlyEditor.value =
        CBModel.valueToBool(
            spec,
            'renderImageOnly'
        );

        renderImageOnlyEditor.changed =
        function ()
        {
            spec.renderImageOnly =
            renderImageOnlyEditor.value;

            specChangedCallback();
        };

        elements[0].appendChild(
            renderImageOnlyEditor.element
        );

        element.appendChild(
            createCSSClassNamesEditorElement(
                spec,
                specChangedCallback
            )
        );


        /* set thumbnail */

        if (
            args.spec.image
        ) {
            imageChooser.src =
            CBImage.toURL(
                args.spec.image,
                "rw960"
            );
        }



        /**
         * @param object chooseArgs
         *
         * @return undefined
         */
        function
        createEditor_handleImageChosen(
        ) // -> undefined
        {
            imageChooser.caption =
            "uploading...";

            CBAjax.call(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            ).then(
                function (
                    imageModel
                ) // -> undefined
                {
                    args.spec.image =
                    imageModel;

                    args.specChangedCallback();

                    imageChooser.src =
                    CBImage.toURL(
                        imageModel,
                        "rw960"
                    );

                    let suggestThumbnailImage =
                    CBModel.valueAsFunction(
                        window.CBViewPageEditor,
                        "suggestThumbnailImage"
                    );

                    if (
                        suggestThumbnailImage
                    ) {
                        suggestThumbnailImage(
                            imageModel
                        );
                    }
                }
            ).catch(
                function (
                    error
                ) // -> undefined
                {
                    CBUIPanel.displayAndReportError(
                        error
                    );
                }
            ).finally(
                function (
                ) // -> undefined
                {
                    imageChooser.caption =
                    "";
                }
            );
        }
        /* createEditor_handleImageChosen() */



        /**
         * @return undefined
         */
        function
        createEditor_handleImageRemoved(
        ) // -> undefined
        {
            args.spec.image =
            undefined;

            args.specChangedCallback();
        }
        /* createEditor_handleImageRemoved() */



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
    ) // -> string|undefined
    {
        let alternativeText =
        CBModel.valueToString(
            spec,
            "alternativeText"
        ).trim();

        if (
            alternativeText !== ""
        ) {
            return alternativeText;
        }

        let captionAsMarkdown =
        CBModel.valueToString(
            spec,
            "captionAsMarkdown"
        ).trim();

        if (
            captionAsMarkdown !== ""
        ) {
            return captionAsMarkdown;
        }

        let cbmessage =
        CBMessageMarkup.messageToText(
            CBModel.valueToString(
                spec,
                "captionAsCBMessage"
            )
        ).trim();

        if (
            cbmessage === ""
        ) {
            return undefined;
        }

        else
        {
            return cbmessage;
        }
    }
    /* CBUISpec_toDescription() */



    /**
     * @return Element
     */
    function
    createAlternativeTextEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let stringEditor =
        CBUIStringEditor2.create();

        stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
            spec,
            "alternativeText",
            "Alternative Text",
            specChangedCallback
        );

        return stringEditor.CBUIStringEditor2_getElement();
    }
    /* createAlternativeTextEditor() */



    /**
     * @return Element
     */
    function
    createCaptionEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let propertyName;
        let title;

        let captionAsMarkdown =
        CBModel.valueToString(
            spec,
            "captionAsMarkdown"
        );

        if (
            captionAsMarkdown.trim() === ""
        ) {
            title =
            "Caption (cbmessage)";

            propertyName =
            "captionAsCBMessage";
        }

        else
        {
            title =
            "Caption (markdown)";

            propertyName =
            "captionAsMarkdown";
        }

        let stringEditor =
        CBUIStringEditor2.create();

        stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
            spec,
            propertyName,
            title,
            specChangedCallback
        );

        return stringEditor.CBUIStringEditor2_getElement();
    }
    /* createAlternativeTextEditor() */



    /**
     * @return Element
     */
    function
    createCSSClassNamesEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let element =
        CBUI.createElement(
            "CBArtworkViewEditor_CSSClassNamesEditor"
        );

        element.appendChild(
            CBUI.createSectionHeader2(`

                    Supported Class Names

                    --- dl
                        --- dt
                        CBArtworkView_captionLeft
                        ---

                        Align the caption text to the left.

                        --- dt
                        CBArtworkView_captionRight
                        ---

                        Align the caption text to the right.
                    ---

            `)
        );

        let elements =
        CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        element.appendChild(
            elements[0]
        );

        let sectionElement =
        elements[1];

        let stringEditor =
        CBUIStringEditor2.create();

        stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
            spec,
            "CSSClassNames",
            "CSS Class Names",
            specChangedCallback
        );

        sectionElement.appendChild(
            stringEditor.CBUIStringEditor2_getElement()
        );

        return element;
    }
    /* createCSSClassNamesEditorElement() */

})();
