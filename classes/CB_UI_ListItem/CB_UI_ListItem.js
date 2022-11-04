/* global
    CBImage,
*/


(function ()
{
    window.CB_UI_ListItem = {
        create:
        CB_UI_ListItem_create,
    };



    /**
     * @return object
     */
    function
    CB_UI_ListItem_create(
    ) // -> object
    {
        let modelCBID;

        let rootElement =
        CB_UI_ListItem_createRootElement();

        let contentElement =
        CB_UI_ListItem_createContentElement();

        rootElement.append(
            contentElement
        );

        let imageContainerElement =
        CB_UI_ListItem_createImageContainerElement();

        contentElement.append(
            imageContainerElement
        );

        let informationContainerElement =
        CB_UI_ListItem_createInformationContainerElement(
            contentElement
        );

        let administrativeTitleElement =
        CB_UI_ListItem_createAdministrativeTitleElement();

        informationContainerElement.append(
            administrativeTitleElement
        );

        let titleElement =
        CB_UI_ListItem_createTitleElement();

        informationContainerElement.append(
            titleElement
        );

        let classNameElement =
        CB_UI_ListItem_createClassNameElement();

        informationContainerElement.append(
            classNameElement
        );

        let CBIDElement =
        CB_UI_ListItem_createCBIDElement();

        informationContainerElement.append(
            CBIDElement
        );

        let editElement =
        CB_UI_ListItem_createEditElement();

        informationContainerElement.append(
            editElement
        );

        let inspectElement =
        CB_UI_ListItem_createInspectElement();

        informationContainerElement.append(
            inspectElement
        );

        // -- accessors



        function
        CB_UI_ListItem_setAdministrativeTitle(
            newAdministrativeTitle
        ) // -> undefined
        {
            administrativeTitleElement.textContent =
            newAdministrativeTitle;
        }



        function
        CB_UI_ListItem_setImageModel(
            newImageModel
        ) // -> undefined
        {
            imageContainerElement.textContent =
            "";

            if (
                newImageModel === null ||
                typeof newImageModel !== "object"
            ) {
                return;
            }

            let pictureElement =
            CBImage.createPictureElementWithMaximumDisplayWidthAndHeight(
                newImageModel,
                "rw640",
                100,
                200,
                ""
            );

            imageContainerElement.append(
                pictureElement
            );
        }



        function
        CB_UI_ListItem_setModelCBID(
            newModelCBID
        ) // -> undefined
        {
            modelCBID =
            newModelCBID;

            CBIDElement.textContent =
            newModelCBID;

            editElement.href =
            `/admin/?c=CBModelEditor&ID=${newModelCBID}`;

            inspectElement.href =
            `/admin/?c=CBModelInspector&ID=${newModelCBID}`;
        }



        function
        CB_UI_ListItem_setModelClassName(
            newModelClassName
        ) // -> undefined
        {
            classNameElement.textContent =
            newModelClassName;
        }



        function
        CB_UI_ListItem_getRootElement(
        ) // -> Element
        {
            return rootElement;
        }



        function
        CB_UI_ListItem_setTitle(
            newTitle
        ) // -> undefined
        {
            titleElement.textContent =
            newTitle;
        }



        return {
            CB_UI_ListItem_setAdministrativeTitle,
            CB_UI_ListItem_setImageModel,
            CB_UI_ListItem_setModelCBID,
            CB_UI_ListItem_setModelClassName,
            CB_UI_ListItem_getRootElement,
            CB_UI_ListItem_setTitle,
        };
    }
    // CB_UI_ListItem_create()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createAdministrativeTitleElement(
    ) // -> Element
    {
        let administrativeTitleElement =
        document.createElement(
            "div"
        );

        administrativeTitleElement.className =
        "CB_UI_ListItem_administrativeTitle_element";

        return (
            administrativeTitleElement
        );
    }
    // CB_UI_ListItem_createAdministrativeTitleElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createCBIDElement(
    ) // -> Element
    {
        let CBIDElement =
        document.createElement(
            "div"
        );

        CBIDElement.className =
        "CB_UI_ListItem_CBID_element";

        return (
            CBIDElement
        );
    }
    // CB_UI_ListItem_createCBIDElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createClassNameElement(
    ) // -> Element
    {
        let classNameElement =
        document.createElement(
            "div"
        );

        classNameElement.className =
        "CB_UI_ListItem_className_element";

        return (
            classNameElement
        );
    }
    // CB_UI_ListItem_createClassNameElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createContentElement(
    ) // -> Element
    {
        let contentElement =
        document.createElement(
            "div"
        );

        contentElement.className =
        "CB_UI_ListItem_content_element";

        return (
            contentElement
        );
    }
    // CB_UI_ListItem_createContentElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createEditElement(
    ) // -> Element
    {
        let editElement =
        document.createElement(
            "a"
        );

        editElement.textContent =
        "edit";

        editElement.className =
        "CB_UI_ListItem_edit_element";

        return (
            editElement
        );
    }
    // CB_UI_ListItem_createEditElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createImageContainerElement(
    ) // -> Element
    {
        let imageContainerElement =
        document.createElement(
            "div"
        );

        imageContainerElement.className =
        "CB_UI_ListItem_imageContainer_element";

        return (
            imageContainerElement
        );
    }
    // CB_UI_ListItem_createImageContainerElement()



    /**
     * @param Element parentElement
     *
     * @return Element
     */
    function
    CB_UI_ListItem_createInformationContainerElement(
        parentElement
    ) // -> Element
    {
        let informationContainerElement =
        document.createElement(
            "div"
        );

        informationContainerElement.className =
        "CB_UI_ListItem_informationContainer_element";

        parentElement.append(
            informationContainerElement
        );

        return informationContainerElement;
    }
    // CB_UI_ListItem_createInformationContainerElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createInspectElement(
    ) // -> Element
    {
        let inspectElement =
        document.createElement(
            "a"
        );

        inspectElement.textContent =
        "inspect";

        inspectElement.className =
        "CB_UI_ListItem_inspect_element";

        return (
            inspectElement
        );
    }
    // CB_UI_ListItem_createInspectElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createRootElement(
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_UI_ListItem_root_element";

        return (
            rootElement
        );
    }
    // CB_UI_ListItem_createRootElement()



    /**
     * @return Element
     */
    function
    CB_UI_ListItem_createTitleElement(
    ) // -> Element
    {
        let titleElement =
        document.createElement(
            "div"
        );

        titleElement.className =
        "CB_UI_ListItem_title_element";

        return (
            titleElement
        );
    }
    // CB_UI_ListItem_createTitleElement()

}
)();
