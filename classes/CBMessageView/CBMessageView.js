/* global
    CBMessageMarkup,
*/


(function () {
    "use strict";

    window.CBMessageView = {
        create: CBMessageView_create,
    };



    /**
     * @return Element
     */
    function
    CBMessageView_create(
    ) // -> Element
    {
        let rootElement = document.createElement(
            "div"
        );

        rootElement.className = (
            "CBMessageView_root_element CBMessageView_default CBContentStyleSheet"
        );

        let contentElement = document.createElement(
            "div"
        );

        contentElement.className = "CBMessageView_content_element";

        rootElement.append(
            contentElement
        );



        /**
         * @param string cbmessage
         *
         * @return undefined
         */
        rootElement.CBMessageView_setCBMessage = function (
            cbmessage
        ) // -> undefined
        {
            CBMessageView_setCBMessage(
                contentElement,
                cbmessage
            );
        };
        /* rootElement.CBMessageView_setCBMessage() */



        return rootElement;
    }
    /* CBMessageView_create() */



    /**
     * @param Element contentElement
     * @param string cbmessage
     *
     * @return undefined
     */
    function
    CBMessageView_setCBMessage(
        contentElement,
        cbmessage
    ) // -> undefined
    {
        contentElement.innerHTML = CBMessageMarkup.cbmessageToHTML(
            cbmessage
        );
    }
    /* CBMessageView_setCBMessage() */

})();
