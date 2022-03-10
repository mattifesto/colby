/* global
    CBAjax,
    CBImage,
    CBUI,
    CBUIImageChooser,
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

        rootEditorElement.textContent =
        "hero 1 editor";

        rootEditorElement.append(
            CB_CBView_Hero1Editor_createImageEditorElement(
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
    CB_CBView_Hero1Editor_createImageEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let rootImageEditorElement =
        document.createElement(
            "div"
        );

        rootImageEditorElement.className =
        "CB_CBView_Hero1Editor_root_element";

        let sectionElement =
        CBUI.createSection();

        rootImageEditorElement.appendChild(
            sectionElement
        );

        let imageChooser =
        CBUIImageChooser.create();

        {
            let imageURL =
            CBImage.toURL(
                spec.CB_CBView_Hero1_wideImage_property,
                "rw960",
                "webp"
            );

            if (
                imageURL !== ""
            ) {
                imageChooser.src =
                imageURL;
            }
        }

        imageChooser.chosen =
        function ()
        {
            CB_CBView_Hero1Editor_handleImageChosen(
                imageChooser,
                spec,
                specChangedCallback
            );
        };

        //imageChooser.removed = createEditor_handleImageRemoved;

        sectionElement.appendChild(
            imageChooser.element
        );

        return rootImageEditorElement;
    }
    // CB_CBView_Hero1Editor_createImageEditorElement()



    /**
     * @param object imageChooser
     * @param object spec
     * @param function specChangedCallback
     *
     * @return undefined
     */
    async function
    CB_CBView_Hero1Editor_handleImageChosen(
        imageChooser,
        spec,
        specChangedCallback
    ) // -> undefined
    {
        try
        {
            imageChooser.caption = "uploading...";

            let imageModel =
            await CBAjax.call(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            );

            spec.CB_CBView_Hero1_wideImage_property =
            imageModel;

            specChangedCallback();

            imageChooser.src =
            CBImage.toURL(
                imageModel,
                "rw960",
                "webp"
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
            imageChooser.caption = "";
        }
    }
    // CB_CBView_Hero1Editor_handleImageChosen()

})();
