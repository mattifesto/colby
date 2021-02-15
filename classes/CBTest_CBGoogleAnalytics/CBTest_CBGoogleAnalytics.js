/* jshint esversion: 8 */
/* global
    CBGoogleAnalytics,
*/


(function () {
    "use strict";

    window.CBTest_CBGoogleAnalytics = {
        CBTest_sendViewItemEvent,
    };

    /**
     * I think we need a CBGoogleAnalytics class that has all the functions
     * necessary to send notifications and that will be called instead of
     * having google notifications elsewhere.
     */


    /**
     * @return Promise -> object
     */
    async function
    CBTest_sendViewItemEvent(
    ){
        CBGoogleAnalytics.sendViewItemEvent();
        CBGoogleAnalytics.sendPurchaseEvent();

        return {
            succeeded: true,
        };
    }
    /* CBTest_addProductToCart() */

})();
