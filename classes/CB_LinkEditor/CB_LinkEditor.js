/* global
    CB_Link,
    CB_UI_StringEditor
*/


(function ()
{
    "use strict";

    let CB_LinkEditor =
    {
        CBUISpec_toDescription,

        CBUISpecEditor_createEditorElement2,

        create:
        CB_LinkEditor_create,
    };

    window.CB_LinkEditor =
    CB_LinkEditor;



    /**
     * @param object linkModel
     *
     * @return string
     */
    function
    CBUISpec_toDescription(
        linkModel
    ) // -> string
    {
        let text =
        CB_Link.getText(
            linkModel
        );

        if (
            text ===
            ""
        ) {
            text =
            "<no text>";
        }

        let url =
        CB_Link.getURL(
            linkModel
        );

        if (
            url ===
            ""
        ) {
            url =
            "<no URL>";
        }

        let description =
        `${text} (${url})`;

        return description;
    }
    // CBUISpec_toDescription()



    // -- CBUISpecEditor interfaces



    /**
     * @param object linkSpec
     * @param function linkSpecChangedEventListener
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement2(
        linkSpec,
        linkSpecChangedEventListener
    ) // -> Element
    {
        let linkEditor =
        CB_LinkEditor.create();

        linkEditor.CB_LinkEditor_setValue(
            linkSpec
        );

        linkEditor.CB_LinkEditor_setChangedEventListener(
            linkSpecChangedEventListener
        );

        let element =
        linkEditor.CB_LinkEditor_getElement();

        return element;
    }
    // CBUISpecEditor_createEditorElement2()



    /**
     * @return object
     */
    function
    CB_LinkEditor_create(
    ) // -> object
    {
        let linkSpec;
        let linkSpecChangedEventListener = function() {};



        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_LinkEditor_root_element";



        // text

        let textEditor =
        CB_UI_StringEditor.create();

        textEditor.CB_UI_StringEditor_setTitle(
            "Text"
        );

        textEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                if (
                    linkSpec ===
                    undefined
                ) {
                    return;
                }

                CB_Link.setText(
                    linkSpec,
                    textEditor.CB_UI_StringEditor_getValue()
                );

                linkSpecChangedEventListener();
            }
        );

        rootElement.append(
            textEditor.CB_UI_StringEditor_getElement()
        );



        // URL

        let urlEditor =
        CB_UI_StringEditor.create();

        urlEditor.CB_UI_StringEditor_setTitle(
            "URL"
        );

        urlEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                if (
                    linkSpec ===
                    undefined
                ) {
                    return;
                }

                CB_Link.setURL(
                    linkSpec,
                    urlEditor.CB_UI_StringEditor_getValue()
                );

                linkSpecChangedEventListener();
            }
        );

        rootElement.append(
            urlEditor.CB_UI_StringEditor_getElement()
        );



        // api

        let api =
        {
            CB_LinkEditor_setChangedEventListener,
            CB_LinkEditor_getElement,
            CB_LinkEditor_setValue,
        };



        // api functions



        /**
         * @param function newLinkSpecChangedEventListener
         *
         * @return undefined
         */
        function
        CB_LinkEditor_setChangedEventListener(
            newLinkSpecChangedEventListener
        ) // -> undefined
        {
            linkSpecChangedEventListener =
            newLinkSpecChangedEventListener;
        }
        // CB_LinkEditor_setChangedEventListener()



        /**
         * @return Element
         */
        function
        CB_LinkEditor_getElement(
        ) // -> Element
        {
            return rootElement;
        }
        // CB_LinkEditor_getElement()



        /**
         * @param object newLinkSpec
         *
         * @return undefined
         */
        function
        CB_LinkEditor_setValue(
            newLinkSpec
        ) // -> undefined
        {
            linkSpec =
            newLinkSpec;

            textEditor.CB_UI_StringEditor_setValue(
                CB_Link.getText(
                    linkSpec
                )
            );

            urlEditor.CB_UI_StringEditor_setValue(
                CB_Link.getURL(
                    linkSpec
                )
            );
        }
        // CB_LinkEditor_setValue()



        return api;
    }
    // CB_LinkEditor_create()

}
)();
