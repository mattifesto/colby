"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBModel,
*/

(function () {

    window.SCPreferences = {
        getOrderNotificationsEmailAddressesCSV,
    };



    /**
     * @param object spec
     *
     * @return string
     */
    function
    getOrderNotificationsEmailAddressesCSV(
        spec
    ) {
        return CBModel.valueToString(
            spec,
            'orderNotificationsEmailAddressesCSV'
        );
    }
    /* getOrderNotificationsEmailAddressesCSV() */

})();
