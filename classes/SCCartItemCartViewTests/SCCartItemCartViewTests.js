"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItemCartViewTests */
/* exported SCTestCartItem1CartView */
/* exported SCTestCartItem2CartView */
/* global
    CBActiveObject,
    CBReleasable,
    CBTest,
    CBUI,
    SCCartItemCartView,
*/



var SCCartItemCartViewTests = {

    /* -- tests -- -- -- -- -- */



    /**
     * @return object|Promise
     */
    CBTest_createElement: function () {
        let firstCartItemSpec = {
            className: "SCTestCartItem1",
        };

        CBActiveObject.activate(firstCartItemSpec);

        let cartItemCartViewContainerElement = SCCartItemCartView.createElement(
            firstCartItemSpec
        );

        let cartItemCartViewElement = (
            cartItemCartViewContainerElement.children[0]
        );

        /* test */

        {
            let actualResult = cartItemCartViewContainerElement.className;

            let expectedResult = [
                "SCCartItemCartView_container",
                "SCCartItemCartView_itemIsAvailable",
            ].join(" ");

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "container element class name test",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* test */

        {
            let actualResult = cartItemCartViewElement.className;
            let expectedResult = "SCTestCartItem1CartView";

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "SCTestCartItem1 class name test",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* test */

        {
            firstCartItemSpec.name = "Bob";

            firstCartItemSpec
            .CBActiveObject
            .tellListenersThatTheObjectDataHasChanged();

            let actualResult = cartItemCartViewElement
            .SCCartItemCartViewTests_name;
            let expectedResult = firstCartItemSpec.name;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "Name test 1",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* test */

        {
            let actualResult = cartItemCartViewElement
            .SCCartItemCartViewTests_updateCount;
            let expectedResult = 1;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "Update count test 1",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* test */

        {
            firstCartItemSpec.name = "Sam";

            firstCartItemSpec
            .CBActiveObject
            .tellListenersThatTheObjectDataHasChanged();

            let actualResult = cartItemCartViewElement
            .SCCartItemCartViewTests_name;
            let expectedResult = firstCartItemSpec.name;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "Name test 2",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* test */

        {
            let actualResult = cartItemCartViewElement
            .SCCartItemCartViewTests_updateCount;
            let expectedResult = 2;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "Update count test 2",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* prepare */

        let secondCartItemSpec = {
            className: "SCTestCartItem2",
        };

        firstCartItemSpec.CBActiveObject.replace(secondCartItemSpec);

        let cartItemCartViewElement2 = cartItemCartViewContainerElement.children[0];

        /* test */

        if (cartItemCartViewElement === cartItemCartViewElement2) {
            return CBTest.generalFailure(
                "Status element comparison test",
                `
                    The cart item status element should have been changed to
                    a different element after the cart item spec was replace
                    with another with a different class name.
                `
            );
        }

        /* test */

        {
            let actualResult = cartItemCartViewElement2.className;
            let expectedResult = "SCTestCartItem2CartView";

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "SCTestCartItem2 class name test",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* prepare */

        let thirdCartItemSpec = {
            className: "SCTestCartItem3",
        };

        secondCartItemSpec.CBActiveObject.replace(thirdCartItemSpec);

        let cartItemCartViewElement3 = cartItemCartViewContainerElement.children[0];

        /* test */

        {
            let actualResult = cartItemCartViewElement3.classList.item(0);
            let expectedResult = "SCCartItemCartView_defaultCartItemCartView";

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "SCTestCartItem3 class name test",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* prepare */

        let fourthCartItemSpec = {
            className: "SCTestCartItem4",
        };

        thirdCartItemSpec.CBActiveObject.replace(fourthCartItemSpec);

        let cartItemCartViewElement4 = cartItemCartViewContainerElement.children[0];

        /* test */

        {
            let actualResult = cartItemCartViewElement4.classList.item(0);
            let expectedResult = "SCCartItemCartView_defaultCartItemCartView";

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "SCTestCartItem4 class name test",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* done */

        return {
            succeeded: true,
        };
    },
    /* CBTest_createElement() */

};



/**
 *
 */
var SCTestCartItem1CartView = {

    /**
     * @param object cartItemSpec
     *
     * @return Element
     */
    SCCartItemCartView_createElement: function (
        activeCartItemSpec
    ) {
        let updateCount = 0;
        let cartItemCartViewElement = CBUI.createElement(
            "SCTestCartItem1CartView"
        );

        activeCartItemSpec.CBActiveObject.addEventListener(
            "theObjectDataHasChanged",
            theObjectDataHasChanged
        );

        activeCartItemSpec.CBActiveObject.addEventListener(
            "theObjectHasBeenReplaced",
            theObjectHasBeenReplaced
        );

        activeCartItemSpec.CBActiveObject.addEventListener(
            "theObjectHasBeenDeactivated",
            theObjectHasBeenDeactivated
        );

        CBReleasable.activate(
            cartItemCartViewElement,
            function () {
                activeCartItemSpec.CBActiveObject.removeEventListener(
                    "theObjectDataHasChanged",
                    theObjectDataHasChanged
                );

                activeCartItemSpec.CBActiveObject.removeEventListener(
                    "theObjectHasBeenReplaced",
                    theObjectHasBeenReplaced
                );

                activeCartItemSpec.CBActiveObject.removeEventListener(
                    "theObjectHasBeenDeactivated",
                    theObjectHasBeenDeactivated
                );
            }
        );

        return cartItemCartViewElement;



        /* -- closures -- -- -- -- -- */



        /**
         * closure in SCTestCartItem1CartView.SCCartItemCartView_createElement()
         *
         * @return undefined
         */
        function theObjectDataHasChanged() {
            updateCount += 1;

            cartItemCartViewElement
            .SCCartItemCartViewTests_updateCount = updateCount;

            cartItemCartViewElement
            .SCCartItemCartViewTests_name = activeCartItemSpec.name;
        }



        /**
         * closure in SCTestCartItem1CartView.SCCartItemCartView_createElement()
         *
         * @param object replacementObject
         *
         * @return undefined
         */
        function theObjectHasBeenReplaced(replacementObject) {
            activeCartItemSpec = replacementObject;
        }



        /**
         * closure in SCTestCartItem1CartView.SCCartItemCartView_createElement()
         *
         * @return undefined
         */
        function theObjectHasBeenDeactivated() {
            activeCartItemSpec = undefined;
        }

    },
    /* SCCartItemCartView_createElement() */

};
/* SCTestCartItem1CartView */



/**
 *
 */
var SCTestCartItem2CartView = {

    /**
     * @param object activeCartItemSpec
     *
     * @return Element
     */
    SCCartItemCartView_createElement: function (
        /* activeCartItemSpec */
    ) {
        let cartItemCartViewElement = CBUI.createElement(
            "SCTestCartItem2CartView"
        );

        CBReleasable.activate(
            cartItemCartViewElement,
            function () {

            }
        );

        return cartItemCartViewElement;
    },
    /* SCCartItemCartView_createElement() */

};
/* SCTestCartItem2CartView */
