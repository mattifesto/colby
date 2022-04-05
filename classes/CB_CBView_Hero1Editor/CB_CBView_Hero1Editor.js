/* global
    CB_UI_ImageChooser,
    CB_UI_StringEditor,
    CBAjax,
    CBImage,
    CBModel,
    CBUIPanel,
    CBUISpec,
    CBUISpecArrayEditor,

    CB_CBView_Hero1Editor_addableClassNames,
*/


(function () {
    "use strict";

    window.CB_CBView_Hero1Editor = {
        CBUISpec_toDescription,
        CBUISpec_toThumbnailURL,
        CBUISpecEditor_createEditorElement2,
    };



    // -- CBUISpec interfaces



    /**
     * @param object viewSpec
     *
     * @return string|undefined
     */
    function
    CBUISpec_toDescription(
        viewSpec
    ) // -> string|undefined
    {
        let potentialDescription =
        CBModel.valueToString(
            viewSpec,
            "CB_CBView_Hero1_administrativeTitle_property"
        ).trim() ||
        CBModel.valueToString(
            viewSpec,
            "CB_CBView_Hero1_alternativeText_property"
        );

        if (
            potentialDescription !== ""
        ) {
            return potentialDescription;
        }

        let subviewSpecs =
        CBModel.valueToArray(
            viewSpec,
            "CB_CBView_Hero1_subviews_property"
        );

        for (
            let subviewIndex = 0;
            subviewIndex < subviewSpecs.length;
            subviewIndex += 1
        ) {
            let description = CBUISpec.specToDescription(
                subviewSpecs[
                    subviewIndex
                ]
            );

            if (description !== undefined) {
                return description;
            }
        }

        return undefined;
    }
    /* CBUISpec_toDescription() */



    /**
     * @param object viewSpec
     *
     * @return string|undefined
     */
    function
    CBUISpec_toThumbnailURL(
        viewSpec
    ) // -> string|undefined
    {
        let potentialImageModel =
        viewSpec.CB_CBView_Hero1_narrowImage_property ||
        viewSpec.CB_CBView_Hero1_wideImage_property;

        if (
            typeof potentialImageModel === "object"
        ) {
            return CBImage.toURL(
                potentialImageModel,
                "rw320"
            );
        }

        let subviewSpecs =
        CBModel.valueToArray(
            viewSpec,
            "CB_CBView_Hero1_subviews_property"
        );

        for (
            let subviewIndex = 0;
            subviewIndex < subviewSpecs.length;
            subviewIndex += 1
        ) {
            let subviewSpec =
            subviewSpecs[
                subviewIndex
            ];

            let thumbnailURL = CBUISpec.specToThumbnailURL(
                subviewSpec
            );

            if (thumbnailURL !== undefined) {
                return thumbnailURL;
            }
        }

        return undefined;
    }
    /* CBUISpec_toThumbnailURL() */



    // -- CBUISpecEditor interfaces



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement2(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let rootEditorElement =
        document.createElement(
            "div"
        );

        rootEditorElement.className =
        "CB_CBView_Hero1Editor_root_element";

        rootEditorElement.append(
            CB_CBView_Hero1Editor_createAdministrativeTitleEditorElement(
                spec,
                specChangedCallback
            )
        );

        rootEditorElement.append(
            CB_CBView_Hero1Editor_createWideImageEditorElement(
                spec,
                specChangedCallback
            )
        );

        rootEditorElement.append(
            CB_CBView_Hero1Editor_createNarrowImageEditorElement(
                spec,
                specChangedCallback
            )
        );

        rootEditorElement.append(
            CB_CBView_Hero1Editor_createAlternativeTextEditorElement(
                spec,
                specChangedCallback
            )
        );

        rootEditorElement.append(
            CB_CBView_Hero1Editor_createImageDestinationURLEditorElement(
                spec,
                specChangedCallback
            )
        );

        rootEditorElement.append(
            CB_CBView_Hero1Editor_createSubviewsEditorElement(
                spec,
                specChangedCallback
            )
        );

        return rootEditorElement;
    }
    // CBUISpecEditor_createEditorElement2()



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_CBView_Hero1Editor_createAdministrativeTitleEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Administrative Title"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                spec,
                'CB_CBView_Hero1_administrativeTitle_property'
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function ()
            {
                let newAdministrativeTitle =
                stringEditor.CB_UI_StringEditor_getValue().trim();

                spec.CB_CBView_Hero1_administrativeTitle_property =
                newAdministrativeTitle;

                specChangedCallback();
            }
        );

        return stringEditor.CB_UI_StringEditor_getElement();
    }
    // CB_CBView_Hero1Editor_createAdministrativeTitleEditorElement()



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_CBView_Hero1Editor_createAlternativeTextEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Alternative Text"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                spec,
                'CB_CBView_Hero1_alternativeText_property'
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function ()
            {
                let newAlternativeText =
                stringEditor.CB_UI_StringEditor_getValue().trim();

                spec.CB_CBView_Hero1_alternativeText_property =
                newAlternativeText;

                specChangedCallback();
            }
        );

        return stringEditor.CB_UI_StringEditor_getElement();
    }
    // CB_CBView_Hero1Editor_createAlternativeTextEditorElement()



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_CBView_Hero1Editor_createImageDestinationURLEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Image Destination URL"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                spec,
                'CB_CBView_Hero1_imageDestinationURL_property'
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function ()
            {
                let newImageDestinationURL =
                stringEditor.CB_UI_StringEditor_getValue().trim();

                spec.CB_CBView_Hero1_imageDestinationURL_property =
                newImageDestinationURL;

                specChangedCallback();
            }
        );

        return stringEditor.CB_UI_StringEditor_getElement();
    }
    // CB_CBView_Hero1Editor_createImageDestinationURLEditorElement()



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_CBView_Hero1Editor_createNarrowImageEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let imageChooser =
        CB_UI_ImageChooser.create();

        imageChooser.CB_UI_ImageChooser_setTitle(
            "Narrow Image"
        );

        imageChooser.CB_UI_ImageChooser_setImage(
            spec.CB_CBView_Hero1_narrowImage_property
        );

        imageChooser.CB_UI_ImageChooser_setImageWasChosenCallback(
            function ()
            {
                CB_CBView_Hero1Editor_handleNarrowImageWasChosen(
                    imageChooser,
                    spec,
                    specChangedCallback
                );
            }
        );

        return imageChooser.CB_UI_ImageChooser_getElement();
    }
    // CB_CBView_Hero1Editor_createNarrowImageEditorElement()



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_CBView_Hero1Editor_createSubviewsEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        if (
            !Array.isArray(
                spec.CB_CBView_Hero1_subviews_property
            )
        ) {
            spec.CB_CBView_Hero1_subviews_property =
            [];
        }

        let specArrayEditor =
        CBUISpecArrayEditor.create(
            {
                addableClassNames:
                CB_CBView_Hero1Editor_addableClassNames,

                specs:
                spec.CB_CBView_Hero1_subviews_property,

                specsChangedCallback:
                specChangedCallback,
            }
        );

        specArrayEditor.title =
        "Subviews";

        return specArrayEditor.element;
    }
    // CB_CBView_Hero1Editor_createSubviewsEditorElement()



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_CBView_Hero1Editor_createWideImageEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let imageChooser =
        CB_UI_ImageChooser.create();

        imageChooser.CB_UI_ImageChooser_setTitle(
            "Wide Image"
        );

        imageChooser.CB_UI_ImageChooser_setImage(
            spec.CB_CBView_Hero1_wideImage_property
        );

        imageChooser.CB_UI_ImageChooser_setImageWasChosenCallback(
            function ()
            {
                CB_CBView_Hero1Editor_handleWideImageWasChosen(
                    imageChooser,
                    spec,
                    specChangedCallback
                );
            }
        );

        return imageChooser.CB_UI_ImageChooser_getElement();
    }
    // CB_CBView_Hero1Editor_createWideImageEditorElement()



    async function
    CB_CBView_Hero1Editor_handleNarrowImageWasChosen(
        imageChooser,
        spec,
        specChangedCallback
    ) // -> undefined
    {
        try
        {
            // imageChooser.caption = "uploading...";

            let imageFile =
            imageChooser.CB_UI_ImageChooser_getImageFile();

            let imageModel =
            await CBAjax.call(
                "CBImages",
                "upload",
                {},
                imageFile
            );

            spec.CB_CBView_Hero1_narrowImage_property =
            imageModel;

            specChangedCallback();

            imageChooser.CB_UI_ImageChooser_setImage(
                imageModel
            );
        }

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );
        }

        finally
        {
            // imageChooser.caption = "";
        }
    }
    // -> CB_CBView_Hero1Editor_handleNarrowImageWasChosen()



    /**
     * @param object imageChooser
     * @param object spec
     * @param function specChangedCallback
     *
     * @return undefined
     */
    async function
    CB_CBView_Hero1Editor_handleWideImageWasChosen(
        imageChooser,
        spec,
        specChangedCallback
    ) // -> undefined
    {
        try
        {
            // imageChooser.caption = "uploading...";

            let imageFile =
            imageChooser.CB_UI_ImageChooser_getImageFile();

            let imageModel =
            await CBAjax.call(
                "CBImages",
                "upload",
                {},
                imageFile
            );

            spec.CB_CBView_Hero1_wideImage_property =
            imageModel;

            specChangedCallback();

            imageChooser.CB_UI_ImageChooser_setImage(
                imageModel
            );
        }

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );
        }

        finally
        {
            // imageChooser.caption = "";
        }
    }
    // CB_CBView_Hero1Editor_handleWideImageWasChosen()

})();
