/* global
    CBModel,
*/


(function ()
{
    "use strict";



    let CB_Moment =
    {
        // accessors

        getAuthorUserModelCBID,
        getCBTimestamp,
        getCreatedTimestamp,
        getImage,

        getImageAlternativeText:
        CB_Moment_getImageAlternativeText,

        getText,

         // functions

         generateMainMomentPageURLPath:
         CB_Moment_generateMainMomentPageURLPath
    };

    window.CB_Moment =
    CB_Moment;



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
    CB_Moment_getImageAlternativeText(
        momentModel
    ) // -> string
    {
        let imageAlternativeText =
        CBModel.valueToString(
            momentModel,
            'CB_Moment_imageAlternativeText_property'
        );

        return imageAlternativeText;
    }
    // CB_Moment_getImageAlternativeText()



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



    // -- functions



    /**
     * @param object momentModel
     *
     * @return string
     *
     *      If no URL can be generated an empty string will be returned.
     */
    function
    CB_Moment_generateMainMomentPageURLPath(
        momentModel
    ) // -> string
    {
        let momentModelCBID =
        CBModel.getCBID(
            momentModel
        );

        if (
            momentModelCBID ===
            undefined
        ) {
            return '';
        }

        let mainMomentPageURLPath =
        `/moment/${momentModelCBID}/`;

        return mainMomentPageURLPath;
    }
    // generateMainMomentPageURL()

}
)();
