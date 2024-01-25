/* global
    CB_UI_StringEditor,
    CBModel,
*/

(function () {
    "use strict";

    window.CB_MomentEditor =
    {
        CBUISpecEditor_createEditorElement2,
    };

    /**
     * @param Object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function CBUISpecEditor_createEditorElement2(
        youtubeChannelSpec,
        specChangedCallback
    ) {
        let rootElement =
            document.createElement(
                "div"
            );

        rootElement.className =
            "CB_YouTubeChannelEditor_root_element";

        rootElement.append(
            createTextEditorElement(
                youtubeChannelSpec,
                specChangedCallback
            )
        );

        rootElement.append(
            createImageAlternativeTextEditorElement(
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
    function createImageAlternativeTextEditorElement(
        momentSpec,
        specChangedCallback
    ) {
        let rootElement =
            document.createElement(
                "div"
            );

        let stringEditor =
            CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Alternative Text"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                momentSpec,
                "CB_Moment_imageAlternativeText_property"
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function () {
                momentSpec.CB_Moment_imageAlternativeText_property =
                    stringEditor.CB_UI_StringEditor_getValue();

                specChangedCallback();
            }
        );

        rootElement.append(
            stringEditor.CB_UI_StringEditor_getElement()
        );

        return rootElement;
    }
    // createImageAlternativeTextEditorElement()



    /**
     * @param object youtubeChannelSpec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function createTextEditorElement(
        momentSpec,
        specChangedCallback
    ) {
        let rootElement =
            document.createElement(
                "div"
            );

        let stringEditor =
            CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Text"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                momentSpec,
                "CB_Moment_text"
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function () {
                momentSpec.CB_Moment_text =
                    stringEditor.CB_UI_StringEditor_getValue();

                specChangedCallback();
            }
        );

        rootElement.append(
            stringEditor.CB_UI_StringEditor_getElement()
        );

        return rootElement;
    }
    // createTextEditorElement()

})();
