/* globals
    CB_Link,
    CB_Link_Array,
    CB_UI,
    CBUINavigationView,
    CBUISpecArrayEditor,
*/


(function ()
{
    "use strict";

    let CB_Link_ArrayEditor =
    {
        CBUISpecEditor_createEditorElement2,

        create:
        CB_Link_ArrayEditor_create,
    };

    window.CB_Link_ArrayEditor =
    CB_Link_ArrayEditor;



    // -- CBUISpecEditor interfaces



    /**
     * @param object linkArraySpec
     * @param function linkArraySpecChangedEventListener
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement2(
        linkArraySpec,
        linkArraySpecChangedEventListener
    ) // -> Element
    {
        let linkArrayEditor =
        CB_Link_ArrayEditor.create();

        linkArrayEditor.CB_Link_ArrayEditor_setValue(
            linkArraySpec
        );

        linkArrayEditor.CB_Link_ArrayEditor_setChangedEventListener(
            linkArraySpecChangedEventListener
        );

        let element =
        linkArrayEditor.CB_Link_ArrayEditor_getElement();

        return element;
    }
    // CBUISpecEditor_createEditorElement2()



    // functions



    /**
     * @return object
     */
    function
    CB_Link_ArrayEditor_create(
    ) // -> object
    {
        let linkArraySpec;
        let linkArraySpecChangedEventListener;



        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_Link_ArrayEditor_root_element";



        let contentElement =
        document.createElement(
            "div"
        );

        rootElement.append(
            contentElement
        );

        contentElement.className =
        "CB_Link_ArrayEditor_content_element";

        contentElement.addEventListener(
            "click",
            function (
            ) // -> undefined
            {
                CB_Link_ArrayEditor_navigateToEditingPanel();
            }
        );


        let titleElement =
        document.createElement(
            "div"
        );

        contentElement.append(
            titleElement
        );

        titleElement.className =
        "CB_Link_ArrayEditor_title_element";



        let descriptionElement =
        document.createElement(
            "div"
        );

        contentElement.append(
            descriptionElement
        );

        descriptionElement.textContent =
        CB_UI.getNonBreakingSpaceCharacter();



        let api =
        {
            CB_Link_ArrayEditor_setChangedEventListener,
            CB_Link_ArrayEditor_getElement,
            CB_Link_ArrayEditor_setTitle,
            CB_Link_ArrayEditor_getValue,
            CB_Link_ArrayEditor_setValue,
        };



        // api functions



        /**
         * @param function newLinkArraySpecChangedEventListener
         */
        function
        CB_Link_ArrayEditor_setChangedEventListener(
            newLinkArraySpecChangedEventListener
        ) // -> void
        {
            linkArraySpecChangedEventListener =
            newLinkArraySpecChangedEventListener;
        }
        // CB_Link_ArrayEditor_setChangedEventListener()



        /**
         * @return Element
         */
        function
        CB_Link_ArrayEditor_getElement(
        ) // -> Element
        {
            return rootElement;
        }
        // CB_Link_ArrayEditor_getElement()



        /**
         * @param string newTitle
         */
        function
        CB_Link_ArrayEditor_setTitle(
            newTitle
        ) // -> undefined
        {
            titleElement.textContent =
            newTitle;
        }
        // CB_Link_ArrayEditor_setTitle()



        /**
         * @return object
         */
        function
        CB_Link_ArrayEditor_getValue(
        ) // -> object|undefined
        {
            return linkArraySpec;
        }
        // CB_Link_ArrayEditor_getValue()



        /**
         * @param <CB_Link_Array spec> newLinkArraySpec
         *
         * @return undefined
         */
        function
        CB_Link_ArrayEditor_setValue(
            newLinkArraySpec
        ) // -> undefined
        {
            linkArraySpec =
            newLinkArraySpec;

            CB_Link_ArrayEditor_updateDescription();
        }
        // CB_Link_ArrayEditor_setValue()



        // functions



        /**
         * @return undefined
         */
        function
        CB_Link_ArrayEditor_navigateToEditingPanel(
        ) // -> undefined
        {
            let editingPanelRootElement =
            document.createElement(
                "div"
            );

            let arrayEditor =
            CBUISpecArrayEditor.create(
                {
                    addableClassNames:
                    [
                        "CB_Link",
                    ],

                    specs:
                    CB_Link_Array.getLinks(
                        linkArraySpec
                    ),

                    specsChangedCallback:
                    function (
                    ) // -> undefined
                    {
                        CB_Link_ArrayEditor_updateDescription();

                        linkArraySpecChangedEventListener();
                    },
                }
            );

            editingPanelRootElement.append(
                arrayEditor.element
            );

            let item =
            {
                element:
                editingPanelRootElement,

                title:
                "Profile Links",
            };

            CBUINavigationView.navigate(
                item
            );
        }
        // CB_Link_ArrayEditor_navigateToEditingPanel()



        /**
         * @return undefined
         */
        function
        CB_Link_ArrayEditor_updateDescription(
        ) // -> undefined
        {
            let arrayOfLinkSpecs =
            CB_Link_Array.getLinks(
                linkArraySpec
            );

            if (
                arrayOfLinkSpecs.length > 0
            ) {
                let linkSpecTexts =
                arrayOfLinkSpecs.map(
                    function (
                        linkSpec
                    ) // -> string
                    {
                        let linkText =
                        CB_Link.getText(
                            linkSpec
                        );

                        if (
                            linkText ===
                            ""
                        ) {
                            linkText =
                            "<no text>";
                        }

                        return linkText;
                    }
                );

                descriptionElement.textContent =
                linkSpecTexts.join(
                    " | "
                );
            }

            else
            {
                descriptionElement.textContent =
                CB_UI.getNonBreakingSpaceCharacter();
            }
        }
        // CB_Link_ArrayEditor_updateDescription()



        return api;
    }
    // CB_Link_ArrayEditor_create()

}
)();
