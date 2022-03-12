/* global
    CB_UI_ImageChooser,
    CB_UI_StringEditor,
    CBAjax,
    CBUIPanel,
*/


(function () {
    "use strict";

    window.CB_CBView_Hero1Editor = {
        CBUISpecEditor_createEditorElement2,
    };



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
    //CB_CBView_Hero1Editor_createAlternativeTextEditorElement()



    /**
     * @param object spec
     * @param functon specChangedCallback
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
