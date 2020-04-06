"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCProductBuyView */
/* globals
    CBArtworkElement,
    CBConvert,
    CBImage,
    CBModel,
    CBUI,
    Colby,
    SCCartItem,
    SCShoppingCart,
*/

var SCProductBuyView = {

    /**
     * @return undefined
     */
    init: function () {
        let elements = document.getElementsByClassName("SCProductBuyView");

        for (let index = 0; index < elements.length; index += 1) {
            SCProductBuyView.render(elements[index]);
        }
    },
    /* init() */


    /**
     * @param Element element
     *
     * @return undefined
     */
    render: function (viewElement) {
        let activeCartItemSpec;

        let information = {};

        {
            let informationAsJSON = CBConvert.valueToString(
                viewElement.dataset.information
            );

            if (informationAsJSON !== "") {
                information = JSON.parse(informationAsJSON);
            }
        }

        {
            let originalCartItemSpec = JSON.parse(
                viewElement.dataset.cartItemSpec
            );

            activeCartItemSpec =
            SCShoppingCart
            .mainCartItemSpecs
            .fetchCartItem(
                originalCartItemSpec
            );

            activeCartItemSpec.CBActiveObject.addEventListener(
                "theObjectHasBeenReplaced",
                function (replacementObject) {
                    activeCartItemSpec = replacementObject;
                }
            );

            /**
             * Since the cart item spec set on this element has the most
             * recently updated data, replace the active cart item spec with
             * this cart item spec.
             */

            SCCartItem.setQuantity(
                originalCartItemSpec,
                SCCartItem.getQuantity(activeCartItemSpec)
            );

            activeCartItemSpec.CBActiveObject.replace(originalCartItemSpec);
        }

        let viewContentElement = CBUI.createElement("CBUI_viewContent");

        viewElement.appendChild(viewContentElement);


        /* image */

        if (!CBModel.valueToBool(information, "hideImage")) {
            let image = CBModel.valueAsModel(activeCartItemSpec, "image");

            if (image !== undefined) {
                let imageContainerElement = CBUI.createElement("CBUI_view");

                viewContentElement.appendChild(imageContainerElement);

                let artworkElement = CBArtworkElement.create(
                    {
                        aspectRatioWidth: image.width,
                        aspectRatioHeight: image.height,
                        maxHeight: 240,
                        URL: CBImage.toURL(image, "rw1280"),
                    }
                );

                imageContainerElement.appendChild(artworkElement);
            }
        }

        let sectionContainerElement = CBUI.createElement(
            "CBUI_sectionContainer"
        );

        viewContentElement.appendChild(sectionContainerElement);

        let sectionElement = CBUI.createElement(
            "CBUI_section CBUI_section_inner"
        );

        sectionContainerElement.appendChild(sectionElement);


        /* title */

        let titleElement = CBUI.createElement("CBUI_text1");

        sectionElement.appendChild(titleElement);

        titleElement.textContent = SCCartItem.getTitle(activeCartItemSpec);


        /* buy button */

        let actionElement = document.createElement("div");

        sectionElement.appendChild(actionElement);

        actionElement.className = "CBUI_action";
        actionElement.textContent = [
            "$",
            CBConvert.centsToDollars(
                CBModel.valueAsInt(activeCartItemSpec, "unitPriceInCents") || 0
            ),
            " Add to Cart",
        ].join("");

        actionElement.addEventListener(
            "click",
            function () {
                render_increaseQuantity();

                location.href = "/view-cart/";
            }
        );


        /* product page link */

        if (CBModel.valueToBool(information, "showProductPageLink")) {
            let actionElement = CBUI.createElement(
                "CBUI_action", "a"
            );

            actionElement.href = information.productPageURL;
            actionElement.textContent = "View Product Page >";

            sectionElement.appendChild(actionElement);
        }


        /* finished */

        return;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function render_increaseQuantity() {
            SCCartItem.setQuantity(
                activeCartItemSpec,
                SCCartItem.getQuantity(activeCartItemSpec) + 1
            );

            activeCartItemSpec
            .CBActiveObject
            .tellListenersThatTheObjectDataHasChanged();
        }
        /* render_increaseQuantity() */
    },
    /* render() */
};
/* SCProductBuyView */


Colby.afterDOMContentLoaded(
    function () {
        SCProductBuyView.init();
    }
);
