"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUI,
    CBUINavigationView,
*/



(function () {

    window.CBUserSettingsManager_shippingAddressManager = {
        CBUserSettingsManager_createElement,
    };



    /**
     * @return Element
     */
    function CBUserSettingsManager_createElement() {
        let elements = CBUI.createElementTree(
            (
                "CBUI_sectionContainer " +
                "CBUserSettingsManager_shippingAddressManager"
            ),
            "CBUI_section",
            "CBUI_sectionItem",
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "title"
        );

        elements[4].textContent = "Shipping Addresses";

        elements[2].appendChild(
            CBUI.createElement(
                "CBUI_navigationArrow"
            )
        );

        elements[2].addEventListener(
            "click",
            function () {
                CBUINavigationView.navigate(
                    {
                        element: createAddressesEditorElement(),
                        title: "Shipping Addresses",
                    }
                );
            }
        );

        return elements[0];
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @return Element
     */
    function createAddressesEditorElement() {
        let element = CBUI.createElement(
            "CBUserSettingsManager_shippingAddressManager_addressesEditor"
        );

        element.textContent = "Addresses Editor";

        return element;
    }
    /* createAddressesEditorElement() */

})();
