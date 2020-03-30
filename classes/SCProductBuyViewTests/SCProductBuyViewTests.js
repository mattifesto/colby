"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCProductBuyViewTests */
/* global
    SCProductBuyView,
*/

var SCProductBuyViewTests = {

    /* -- tests -- -- -- -- -- */

    /**
     * @return object|Promise
     */
    CBTest_render: function () {
        /* test 1 */
        {
            let element = document.createElement("div");

            element.dataset.cartItemSpec = JSON.stringify(
                {
                    className: "SCProductCartItem",
                    productCode: "SCProductBuyViewTests_0001",
                }
            );

            SCProductBuyView.render(element);
        }
        /* test 1 */

        return {
            succeeded: true,
        };
    },
    /* CBTest_initElement() */
};
/* SCProductBuyViewTests */
