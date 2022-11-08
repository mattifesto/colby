/* global
    CB_UI_ListItem,
    CB_UI_StringEditor,
    CBAjax,
    CBUINavigationView,
    CBUIPanel,
    CB_UI_Selector,

    CB_Admin_ModelSearch_modelClassNameOptions,
*/


(function ()
{
    let global_searchHasBeenRequested;
    let global_isCurrentlyPerformingSearch;

    let global_search_query;
    let global_search_modelClassName;

    let global_resultsElement;




    {
        let mainElement =
        document.getElementsByTagName(
            "main"
        )[0];

        let navigationView =
        CBUINavigationView.create();

        mainElement.append(
            navigationView.element
        );

        CB_Admin_ModelSearch_createUserInterface();
    }



    /**
     * @return Element
     */
    function
    CB_Admin_ModelSearch_createClassNameSelectorElement(
    ) // -> Element
    {
        let selectorController =
        CB_UI_Selector.create();

        selectorController.CB_UI_Selector_setTitle(
            "Class Name"
        );

        selectorController.CB_UI_Selector_setArrayOfOptions(
            CB_Admin_ModelSearch_modelClassNameOptions
        );

        selectorController.CB_UI_Selector_setChangedEventListenter(
            function ()
            {
                global_search_modelClassName =
                selectorController.CB_UI_Selector_getValue();

                CB_Admin_ModelSearch_performSearch();
            }
        );

        let selectorElement =
        selectorController.CB_UI_Selector_getElement();

        return selectorElement;
    }
    // CB_Admin_ModelSearch_createClassNameSelectorElement()



    /**
     * @return undefined
     */
    function
    CB_Admin_ModelSearch_createUserInterface(
    ) // -> undefined
    {
        let rootNavigationPanelElement =
        document.createElement(
            "div"
        );

        rootNavigationPanelElement.append(
            CB_Admin_ModelSearch_createClassNameSelectorElement()
        );

        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Search for"
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                global_search_query =
                stringEditor.CB_UI_StringEditor_getValue();

                CB_Admin_ModelSearch_performSearch();
            }
        );

        rootNavigationPanelElement.append(
            stringEditor.CB_UI_StringEditor_getElement()
        );

        global_resultsElement =
        document.createElement(
            "div"
        );

        rootNavigationPanelElement.append(
            global_resultsElement
        );

        CBUINavigationView.navigate(
            {
                element:
                rootNavigationPanelElement,

                title:
                "Search Models",
            }
        );
    }
    // CB_Admin_ModelSearch_createUserInterface()



    /**
     * @return Promise -> undefined
     */
    async function
    CB_Admin_ModelSearch_performSearch(
    ) // -> Promise -> undefined
    {
        try
        {
            if (
                global_isCurrentlyPerformingSearch === true
            ) {
                global_searchHasBeenRequested =
                true;

                return;
            }

            global_searchHasBeenRequested =
            false;

            global_isCurrentlyPerformingSearch =
            true;

            let searchResults =
            await CBAjax.call2(
                "CB_Ajax_ModelSearch_performSearch",
                {
                    CB_Ajax_ModelSearch_performSearch_modelClassName:
                    global_search_modelClassName,

                    CB_Ajax_ModelSearch_performSearch_searchQuery:
                    global_search_query,
                }
            );

            global_resultsElement.textContent =
            "";

            let itemsContainerElement =
            document.createElement(
                "div"
            );

            global_resultsElement.append(
                itemsContainerElement
            );

            searchResults.forEach(
                function (
                    searchResult
                ) // -> undefined
                {
                    let listItem =
                    CB_UI_ListItem.create();

                    listItem.
                    CB_UI_ListItem_setAbsolutePageURL(
                        searchResult.
                        CB_AdministrativeSearchResult_absoluteURLPath
                    );

                    listItem.
                    CB_UI_ListItem_setAdministrativeTitle(
                        searchResult.
                        CB_AdministrativeSearchResult_administrativeTitle
                    );

                    listItem.
                    CB_UI_ListItem_setImageModel(
                        searchResult.
                        CB_AdministrativeSearchResult_associatedImageModel
                    );

                    listItem.
                    CB_UI_ListItem_setModelCBID(
                        searchResult.
                        CB_AdministrativeSearchResult_CBID
                    );

                    listItem.
                    CB_UI_ListItem_setModelClassName(
                        searchResult.
                        CB_AdministrativeSearchResult_className
                    );

                    listItem.
                    CB_UI_ListItem_setTitle(
                        searchResult.
                        CB_AdministrativeSearchResult_title
                    );

                    itemsContainerElement.append(
                        listItem.
                        CB_UI_ListItem_getRootElement()
                    );
                }
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
            global_isCurrentlyPerformingSearch =
            false;

            if (
                global_searchHasBeenRequested === true
            ) {
                CB_Admin_ModelSearch_performSearch();
            }
        }
    }
    // CB_Admin_ModelSearch_performSearch()

}
)();
