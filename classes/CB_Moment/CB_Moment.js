/* global
    CBModel,
*/


(function () {

    window.CB_Moment = {
        getText,
    };



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
