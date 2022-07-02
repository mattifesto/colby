/* global
    CB_UI_StringEditor,
    CBModel,
*/


(function ()
{
    "use strict";

    window.CB_YouTubeChannelEditor =
    {
        CBUISpecEditor_createEditorElement2,
    };



    /**
     * @param Object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement2(
        youtubeChannelSpec,
        specChangedCallback
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_YouTubeChannelEditor_root_element";

        rootElement.append(
            CB_YouTubeChannelEditor_createTitleEditorElement(
                youtubeChannelSpec,
                specChangedCallback
            )
        );

        rootElement.append(
            CB_YouTubeChannelEditor_createChannelIDEditorElement(
                youtubeChannelSpec,
                specChangedCallback
            )
        );

        rootElement.append(
            CB_YouTubeChannelEditor_createAPIKeyEditorElement(
                youtubeChannelSpec,
                specChangedCallback
            )
        );

        return rootElement;
    }
    // CBUISpecEditor_createEditorElement2()



    /**
     * @param object youtubeChannelSpec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_YouTubeChannelEditor_createAPIKeyEditorElement(
        youtubeChannelSpec,
        specChangedCallback
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "API Key"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                youtubeChannelSpec,
                "CB_YouTubeChannel_apiKey_property"
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                youtubeChannelSpec.CB_YouTubeChannel_apiKey_property =
                stringEditor.CB_UI_StringEditor_getValue();

                specChangedCallback();
            }
        );

        rootElement.append(
            stringEditor.CB_UI_StringEditor_getElement()
        );

        return rootElement;
    }
    //CB_YouTubeChannelEditor_createAPIKeyEditorElement()



    /**
     * @param object youtubeChannelSpec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_YouTubeChannelEditor_createChannelIDEditorElement(
        youtubeChannelSpec,
        specChangedCallback
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Channel ID"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                youtubeChannelSpec,
                "CB_YouTubeChannel_channelID_property"
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                youtubeChannelSpec.CB_YouTubeChannel_channelID_property =
                stringEditor.CB_UI_StringEditor_getValue();

                specChangedCallback();
            }
        );

        rootElement.append(
            stringEditor.CB_UI_StringEditor_getElement()
        );

        return rootElement;
    }
    // CB_YouTubeChannelEditor_createChannelIDEditorElement()



    /**
     * @param object youtubeChannelSpec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CB_YouTubeChannelEditor_createTitleEditorElement(
        youtubeChannelSpec,
        specChangedCallback
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Title"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                youtubeChannelSpec,
                "CB_YouTubeChannel_title_property"
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                youtubeChannelSpec.CB_YouTubeChannel_title_property =
                stringEditor.CB_UI_StringEditor_getValue();

                specChangedCallback();
            }
        );

        rootElement.append(
            stringEditor.CB_UI_StringEditor_getElement()
        );

        return rootElement;
    }
    // CB_YouTubeChannelEditor_createTitleEditorElement()

}
)();
