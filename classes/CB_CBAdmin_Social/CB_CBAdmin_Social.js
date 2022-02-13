/* global
    CB_UI_KeyValue,
    CBAjax,
    CBErrorHandler,
    CBModel,
*/


(function () {
    "use strict";

    document.addEventListener(
        "DOMContentLoaded",
        function () {
            afterDOMContentLoaded();
        }
    );


    /**
     * @return undefined
     */
    function
    afterDOMContentLoaded(
    ) {
        let element = document.getElementsByClassName(
            "CB_CBAdmin_Social_element"
        )[0];

        element.append(
            createYouTubeStatusElement()
        );
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function
    createYouTubeStatusElement(
    ) {
        let element = document.createElement(
            "div"
        );

        element.className = "CBStatusAdminPage_YouTubeStatus_element";

        (async function () {
            try {
                let statistics = await CBAjax.call2(
                    'CB_Ajax_YouTube_GetStatistics'
                );

                if (statistics === null) {
                    return;
                }

                let viewCount = CBModel.valueAsInt(
                    statistics,
                    "items.[0].statistics.viewCount"
                );

                let subscriberCount = CBModel.valueAsInt(
                    statistics,
                    "items.[0].statistics.subscriberCount"
                );

                let viewsKeyValue = CB_UI_KeyValue.create();

                viewsKeyValue.CB_UI_KeyValue_setKey(
                    "YouTube Views"
                );

                viewsKeyValue.CB_UI_KeyValue_setValue(
                    viewCount
                );

                element.append(
                    viewsKeyValue.CB_UI_KeyValue_getElement()
                );

                let subscribersKeyValue = CB_UI_KeyValue.create();

                subscribersKeyValue.CB_UI_KeyValue_setKey(
                    "YouTube Subscribers"
                );

                subscribersKeyValue.CB_UI_KeyValue_setValue(
                    subscriberCount
                );

                element.append(
                    subscribersKeyValue.CB_UI_KeyValue_getElement()
                );
            } catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );
            }
        })();

        return element;
    }
    /* createYouTubeStatusElement() */

})();
