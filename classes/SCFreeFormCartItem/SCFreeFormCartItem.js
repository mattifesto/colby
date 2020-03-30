"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBModel,
*/



(function () {



    window.SCFreeFormCartItem = {
        SCCartItem_getMaximumQuantity,
        SCCartItem_specsRepresentTheSameProduct,
    };



    /**
     * @param object cartItemModel
     *
     * @return number
     */
    function SCCartItem_getMaximumQuantity(
        /* cartItemModel */
    ) {
        return 1;
    }
    /* SCCartItem_getMaximumQuantity() */



    /**
     * @param object cartItemModelA
     * @param object cartItemModelB
     *
     * @return bool
     */
    function SCCartItem_specsRepresentTheSameProduct(
        cartItemModelA,
        cartItemModelB
    ) {
        let productCBIDA = CBModel.valueAsCBID(
            cartItemModelA,
            "productCBID"
        );

        let productCBIDB = CBModel.valueAsCBID(
            cartItemModelB,
            "productCBID"
        );

        return productCBIDA === productCBIDB;
    }
    /* SCCartItem_specsRepresentTheSameProduct() */

})();
