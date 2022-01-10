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
     * @TODO 2022_01_09
     *
     *      Rename to go better with attostamp usage.
     *
     * @param object momentModel
     *
     * @return int|undefined
     */
    function
    getCreatedTimestamp(
        momentModel
    ) {
        let timestamp = CBModel.valueAsInt(
            momentModel,
            'CB_Moment_attostamp_property.CB_Attostamp_unixTimestamp_property'
        );

        if (
            timestamp === undefined
        ) {
            timestamp = CBModel.valueAsInt(
                momentModel,
                'CB_Moment_createdTimestamp'
            );
        }

        return timestamp;
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
