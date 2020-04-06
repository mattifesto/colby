"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCProductCartItem */
/* global
    CBModel,
*/



var SCProductCartItem = {

    /* -- SCCartItem interfaces -- -- -- -- -- */



    /**
     * @param object specA
     * @param object specB
     *
     * @return bool
     */
    SCCartItem_specsRepresentTheSameProduct: function (
        specA,
        specB
    ) {
        return (
            CBModel.valueToString(specA, "productCode") ===
            CBModel.valueToString(specB, "productCode")
        );
    },

};
