/* global
    CBModel,
*/


(function () {

    window.CB_Moment = {
        getAuthorUserModelCBID,
        getCreatedTimestamp,
        getText,
    };



    /* -- accessors -- */



    /**
     * @param object momentModel
     *
     * @return CBID|undefined
     */
    function
    getAuthorUserModelCBID(
        momentModel
    ) {
        return CBModel.valueAsCBID(
            momentModel,
            'CB_Moment_authorUserModelCBID'
        );
    }
    /* getAuthorUserModelCBID() */



    /**
     * @param object momentModel
     *
     * @return int|undefined
     */
    function
    getCreatedTimestamp(
        momentModel
    ) {
        return CBModel.valueAsInt(
            momentModel,
            'CB_Moment_createdTimestamp'
        );
    }
    /* getCreatedTimestamp() */



    /**
     * @param object momentModel
     *
     * @return string
     */
    function
    getText(
        momentModel
    ) {
        return CBModel.valueToString(
            momentModel,
            'CB_Moment_text'
        );
    }
    /* getText() */

})();
