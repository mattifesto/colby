/* global
    CBConvert,
    CBException,
    CBModel,
*/

(function () {
    "use strict";

    window.SCShippingAddress = {
        getCountryCBID: SCShippingAddress_getCountryCBID,
        setCountryCBID: SCShippingAddress_setCountryCBID,
        getSpecialInstructions: SCShippingAddress_getSpecialInstructions,
        setSpecialInstructions: SCShippingAddress_setSpecialInstructions,

        fetchLocalSpec: SCShippingAddress_fetchLocalSpec,
        saveLocalSpec: SCShippingAddress_saveLocalSpec,
    };



    /* -- accessors -- */



    /**
     * @param object shippingAddressModel
     *
     * @return CBID|undefined
     */
    function
    SCShippingAddress_getCountryCBID(
        shippingAddressModel
    ) {
        return CBModel.valueAsCBID(
            shippingAddressModel,
            "country"
        );
    }
    /* SCShippingAddress_getCountryCBID() */



    /**
     * @param object shippingAddressSpec
     * @param CBID|undefined countryCBID
     *
     * @return undefined
     */
    function
    SCShippingAddress_setCountryCBID(
        shippingAddressSpec,
        countryCBID
    ) {
        let countryCBIDAsCBID;

        if (countryCBID !== undefined) {
            countryCBIDAsCBID = CBConvert.valueAsCBID(
                countryCBID
            );

            if (countryCBIDAsCBID === undefined) {
                throw CBException.withValueRelatedError(
                    Error("The countryCBID argument is not a valid CBID."),
                    countryCBID,
                    "c5e80126a16eb8a0a561d9cf3b6af6972a497ea4"
                );
            }
        }

        shippingAddressSpec.country = countryCBIDAsCBID;
    }
    /* SCShippingAddress_setCountryCBID() */



    /**
     * @param object shippingAddressModel
     *
     * @return string
     */
    function
    SCShippingAddress_getSpecialInstructions(
        shippingAddressModel
    ) {
        return CBModel.valueToString(
            shippingAddressModel,
            "special-instructions"
        );
    }
    /* SCShippingAddress_getSpecialInstructions() */



    /**
     * @param object shippingAddressSpec
     * @param string specialInstructions
     *
     * @return undefined
     */
    function
    SCShippingAddress_setSpecialInstructions(
        shippingAddressSpec,
        specialInstructions
    ) {
        let specialInstructionsString = CBConvert.valueToString(
            specialInstructions
        );

        shippingAddressSpec["special-instructions"] = specialInstructionsString;
    }
    /* SCShippingAddress_setSpecialInstructions() */



    /* -- functions -- */



    /**
     * @return object
     *
     *      This function fetches the SCShippingAddress spec stored in local
     *      storage in the browser. If there is no spec stored in local storage
     *      an empty SCShippingAddress spec is returned.
     */
    function
    SCShippingAddress_fetchLocalSpec(
    ) {
        let shippingAddressSpec;

        try {
            let shippingAddressSpecAsJSON = localStorage.getItem(
                "shippingAddress"
            );

            shippingAddressSpec = CBConvert.valueAsObject(
                JSON.parse(
                    shippingAddressSpecAsJSON
                )
            );
        } catch (error) { }

        if (shippingAddressSpec === undefined) {
            shippingAddressSpec = {};
        }

        CBModel.setClassName(
            shippingAddressSpec,
            "SCShippingAddress"
        );

        return shippingAddressSpec;
    }
    /* SCShippingAddress_fetchLocalSpec() */



    /**
     * @param object shippingAddressSpec
     *
     * @return undefined
     */
    function
    SCShippingAddress_saveLocalSpec(
        shippingAddressSpec
    ) {
        let shippingAddressSpecAsModel = CBConvert.valueAsModel(
            shippingAddressSpec
        );

        let shippingAddressSpecClassName = CBModel.getClassName(
            shippingAddressSpec
        );

        if (
            shippingAddressSpecAsModel === undefined ||
            shippingAddressSpecClassName !== "SCShippingAddress"
        ) {
            throw CBException.withValueRelatedError(
                Error(
                    CBConvert.stringToCleanLine(`
                        The shippingAddressSpec argument is not a valid
                        SCShippingAddress spec.
                    `)
                ),
                shippingAddressSpec,
                "86f9526862f4fec45a1498a4bed2fa1848b2ed72"
            );
        }

        let shippingAddressSpecAsJSON = JSON.stringify(
            shippingAddressSpecAsModel
        );

        localStorage.setItem(
            "shippingAddress",
            shippingAddressSpecAsJSON
        );
    }
    /* SCShippingAddress_saveLocalSpec() */

})();
