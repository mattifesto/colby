"use strict";
/* jshint strict: global */
/* exported SCShippingAddressEditorView_Tests */
/* global
    CBTest,
    SCShippingAddress,
*/

var SCShippingAddressEditorView_Tests = {

    /**
     * @NOTE 2021_02_11
     *
     *      The function this test originally tested has been replaced and this
     *      test should maybe move to SCShippingAddress tests.
     *
     * @return object
     */
    CBTest_storedShippingAddressModel(
    ) {
        let caughtError;

        let originalShippingAddressModel = SCShippingAddress.fetchLocalSpec();

        {
            let isValid =
            SCShippingAddressEditorView_Tests.shippingAddressModelIsValid(
                originalShippingAddressModel
            );

            if (!isValid) {
                return CBTest.valueIssueFailure(
                    "original shipping address model",
                    originalShippingAddressModel,
                    "The original shipping address model is not valid."
                );
            }
        }

        try {
            /* unset shipping address */

            localStorage.removeItem(
                "shippingAddress"
            );

            {
                let shippingAddressModel = SCShippingAddress.fetchLocalSpec();

                let isValid =
                SCShippingAddressEditorView_Tests.shippingAddressModelIsValid(
                    shippingAddressModel
                );

                if (!isValid) {
                    return CBTest.valueIssueFailure(
                        "unset shipping address model",
                        shippingAddressModel,
                        "The unset shipping address model is not valid."
                    );
                }
            }

            /* valid shipping address */

            localStorage.setItem(
                "shippingAddress",
                JSON.stringify(
                    {}
                )
            );

            {
                let shippingAddressModel = SCShippingAddress.fetchLocalSpec();

                let isValid =
                SCShippingAddressEditorView_Tests.shippingAddressModelIsValid(
                    shippingAddressModel
                );

                if (!isValid) {
                    return CBTest.valueIssueFailure(
                        "valid shipping address model",
                        shippingAddressModel,
                        "The valid shipping address model is not valid."
                    );
                }
            }

            /* invalid shipping address */

            localStorage.setItem(
                "shippingAddress",
                "bad bad bad"
            );

            {
                let shippingAddressModel = SCShippingAddress.fetchLocalSpec();

                let isValid =
                SCShippingAddressEditorView_Tests.shippingAddressModelIsValid(
                    shippingAddressModel
                );

                if (!isValid) {
                    return CBTest.valueIssueFailure(
                        "invalid shipping address model",
                        shippingAddressModel,
                        "The invalid shipping address model is not valid."
                    );
                }
            }
        } catch (error) {
            caughtError = error;
        }

        if (typeof originalShippingAddressModel === "object") {
            localStorage.setItem(
                "shippingAddress",
                JSON.stringify(
                    originalShippingAddressModel
                )
            );
        }

        if (caughtError) {
            throw caughtError;
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_storedShippingAddressModel() */


    /**
     * @param mixed model
     *
     * @return bool
     */
    shippingAddressModelIsValid: function (model) {
        if (model === null) {
            return false;
        }

        let type = typeof model;

        if (
            type !== "object" &&
            type !== "undefined"
        ) {
            return false;
        }

        return true;
    },
    /* shippingAddressModelIsValid() */
};
