/* global
    CBModel,
*/


(function ()
{
    "use strict";

    window.CB_Moment = {
        getAuthorUserModelCBID,
        getCBTimestamp,
        getCreatedTimestamp,
        getImage,
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
     * @return object|undefined
     */
    function
    getCBTimestamp(
        momentModel
    ) {
        return CBModel.valueAsModel(
            momentModel,
            'CB_Moment_cbtimestamp_property',
            'CB_Timestamp'
        );
    }
    /* getCBTimestamp() */



    /**
     * @TODO 2022_01_09
     *
     *      Rename to go better with cbtimestamp usage.
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
            'CB_Moment_cbtimestamp_property.CB_Timestamp_unixTimestamp_property'
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
     * @return object|undefined
     */
    function
    getImage(
        momentModel
    ) {
        return CBModel.valueAsModel(
            momentModel,
            "CB_Moment_imageModel_property",
            "CBImage"
        );
    }
    /* getImage() */



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

}
)();
