"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBActiveArrayTests */
/* global
    CBActiveArray,
    CBActiveObject,
    CBModel,
    CBTest,
*/

var CBActiveArrayTests = {

    /**
     * @return object|Promise
     */
    CBTest_deactivate: function () {
        let cartItems = [
            {
                className: "foo",
                productCode: "1",
            },
            {
                className: "foo",
                productCode: "2",
            },
            {
                className: "foo",
                productCode: "3",
            },
            {
                className: "foo",
                productCode: "4",
            },
        ];

        let activeArray = CBActiveArray.createPod();

        cartItems.forEach(
            function (cartItem) {
                CBActiveObject.activate(cartItem);
                activeArray.push(cartItem);
            }
        );

        {
            let actualResult = activeArray.length;
            let expectedResult = 4;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "length === 4",
                    actualResult,
                    expectedResult
                );
            }
        }

        cartItems[2].CBActiveObject.deactivate();

        {
            let actualResult = activeArray.length;
            let expectedResult = 3;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "length === 3",
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_deactivate() */


    /**
     * @return object|Promise
     */
    CBTest_events: function () {
        let anItemWasAdded_lastItem;
        let anItemWasAdded_count = 0;
        let somethingChanged_count = 0;

        let activeArray = CBActiveArray.createPod();

        activeArray.addEventListener(
            "anItemWasAdded",
            function handleAnItemWasAdded(item) {
                anItemWasAdded_count += 1;
                anItemWasAdded_lastItem = item;
            }
        );

        activeArray.addEventListener(
            "somethingChanged",
            function handleSomethingChanged(item) {
                somethingChanged_count += 1;
            }
        );

        {
            let sourceID;

            try {
                activeArray.addEventListener(
                    "notARealEventType",
                    function () {}
                );
            } catch (error) {
                sourceID = error.CBException.sourceID;
            }

            let expectedSourceID = "baf266926a2fbfb4c1341293f366f8400f048be4";

            if (sourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "addEventListener for bad event type",
                    sourceID,
                    expectedSourceID
                );
            }
        }

        {
            let item0 = { name: "item0" };
            let item1 = { name: "item1" };

            CBActiveObject.activate(item0);
            CBActiveObject.activate(item1);

            activeArray.push(item0);

            if (anItemWasAdded_count !== 1) {
                return CBTest.resultMismatchFailure(
                    "first item added count check",
                    anItemWasAdded_count,
                    1
                );
            }

            if (somethingChanged_count !== 1) {
                return CBTest.resultMismatchFailure(
                    "somethingChanged_count === 1",
                    somethingChanged_count,
                    1
                );
            }

            if (anItemWasAdded_lastItem !== item0) {
                return CBTest.resultMismatchFailure(
                    "first item added item check",
                    anItemWasAdded_lastItem,
                    item0
                );
            }

            if (activeArray.length !== 1) {
                return CBTest.resultMismatchFailure(
                    "first item added length check",
                    activeArray.length,
                    1
                );
            }

            activeArray.push(item1);

            if (anItemWasAdded_count !== 2) {
                return CBTest.resultMismatchFailure(
                    "second item added count check",
                    anItemWasAdded_count,
                    2
                );
            }

            if (somethingChanged_count !== 2) {
                return CBTest.resultMismatchFailure(
                    "somethingChanged_count === 2",
                    somethingChanged_count,
                    2
                );
            }

            if (anItemWasAdded_lastItem !== item1) {
                return CBTest.resultMismatchFailure(
                    "second item added item check",
                    anItemWasAdded_lastItem,
                    item1
                );
            }

            if (activeArray.length !== 2) {
                return CBTest.resultMismatchFailure(
                    "second item added length check",
                    activeArray.length,
                    2
                );
            }

            let itemAtIndex0 = activeArray.item(0);

            if (itemAtIndex0 !== item0) {
                return CBTest.resultMismatchFailure(
                    "first item added item check",
                    itemAtIndex0,
                    item0
                );
            }

            let itemAtIndex1 = activeArray.item(1);

            if (itemAtIndex1 !== item1) {
                return CBTest.resultMismatchFailure(
                    "second item added item check",
                    itemAtIndex1,
                    item1
                );
            }

            item0.CBActiveObject.tellListenersThatTheObjectDataHasChanged();
            item0.CBActiveObject.tellListenersThatTheObjectDataHasChanged();
            item1.CBActiveObject.tellListenersThatTheObjectDataHasChanged();

            if (somethingChanged_count !== 5) {
                return CBTest.resultMismatchFailure(
                    "somethingChanged_count === 5",
                    somethingChanged_count,
                    5
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_events() */


    /**
     * @return object|Promise
     */
    CBTest_find: function () {
        let cartItems = [
            {
                className: "foo",
                productCode: "1",
            },
            {
                className: "foo",
                productCode: "2",
            },
            {
                className: "foo",
                productCode: "3",
            },
            {
                className: "foo",
                productCode: "4",
            },
        ];

        let activeArray = CBActiveArray.createPod();

        cartItems.forEach(
            function (cartItem) {
                CBActiveObject.activate(cartItem);
                activeArray.push(cartItem);
            }
        );

        let actualResult = activeArray.find(
            function (cartItem) {
                return cartItem.productCode === "3";
            }
        );

        let expectedResult = cartItems[2];

        if (actualResult !== expectedResult) {
            return CBTest.resultMismatchFailure(
                "find",
                actualResult,
                expectedResult
            );
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_find() */


    /**
     * @return object|Promise
     */
    CBTest_general: function () {
        let activeArray = CBActiveArray.createPod();

        {
            let sourceID;

            try {
                activeArray.push(5);
            } catch (error) {
                sourceID = error.CBException.sourceID;
            }

            let expectedSourceID = "acec6bc55ba1b76acaeb030acbfcef55cb969db3";

            if (sourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "push non-active item",
                    sourceID,
                    expectedSourceID
                );
            }
        }

        {
            let sourceID;
            let activeObject = {};

            CBActiveObject.activate(activeObject);

            activeArray.push(activeObject);

            try {
                activeArray.push(activeObject);
            } catch (error) {
                sourceID = error.CBException.sourceID;
            }

            let expectedSourceID = "64033117f4fdb3abf482532e3bf90a81827d2961";

            if (sourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "push item already in array",
                    sourceID,
                    expectedSourceID
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_general() */


    /**
     * @return object|Promise
     */
    CBTest_slice: function () {
        let cartItems = [
            {
                className: "foo",
                productCode: "1",
            },
            {
                className: "foo",
                productCode: "2",
            },
            {
                className: "foo",
                productCode: "3",
            },
            {
                className: "foo",
                productCode: "4",
            },
        ];

        let activeArray = CBActiveArray.createPod();

        cartItems.forEach(
            function (cartItem) {
                CBActiveObject.activate(cartItem);
                activeArray.push(cartItem);
            }
        );

        let actualResult = activeArray.slice();
        let expectedResult = cartItems;

        if (!CBModel.equals(actualResult, expectedResult)) {
            return CBTest.resultMismatchFailure(
                "slice",
                actualResult,
                expectedResult
            );
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_slice() */
};
