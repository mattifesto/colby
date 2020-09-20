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


(function() {

    window.SCProductBuyView = {
        render,
    };

    Colby.afterDOMContentLoaded(
        function () {
            afterDOMContentLoaded();
        }
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded(
    ) {
        let elements = document.getElementsByClassName(
            "SCProductBuyView"
        );

        for (let index = 0; index < elements.length; index += 1) {
            render(
                elements[index]
            );
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @param object activeCartItemSpec
     *
     * @return Element
     */
    function createBuyButtonElement(
        activeCartItemSpec
    ) {
        let elements = CBUI.createElementTree(
            (
                "CBUI_container_flexCenterHorizontal" +
                " CBUI_container_paddingHalfTopBottom"
            ),
            "CBUI_button1"
        );

        let buyButtonElement = elements[1];

        let unitPriceInDollars = CBConvert.centsToDollars(
            SCCartItem.getUnitPriceInCents(
                activeCartItemSpec
            )
        );

        buyButtonElement.textContent = `$${unitPriceInDollars} Add To Cart`;

        buyButtonElement.addEventListener(
            "click",
            function () {
                increaseQuantity(
                    activeCartItemSpec
                );

                location.href = "/view-cart/";
            }
        );

        return elements[0];
    }
    /* createBuyButtonElement() */



    /**
     * @param string productPageURL
     *
     * @return Element
     */
    function createProductPageLinkElement(
        productPageURL
    ) {
        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section CBUI_section_inner",
            [
                "CBUI_action",
                "a",
            ]
        );

        let actionElement = elements[2];

        actionElement.href = productPageURL;
        actionElement.textContent = "View Product Page >";

        return elements[0];
    }
    /* createProductPageLinkElement() */



    /**
     * @return undefined
     */
    function increaseQuantity(
        activeCartItemSpec
    ) {
        SCCartItem.setQuantity(
            activeCartItemSpec,
            SCCartItem.getQuantity(activeCartItemSpec) + 1
        );

        activeCartItemSpec
        .CBActiveObject
        .tellListenersThatTheObjectDataHasChanged();
    }
    /* increaseQuantity() */



    /**
     * @param Element element
     *
     * @return undefined
     */
    function render(
        viewElement
    ) {
        let activeCartItemSpec;

        let information = {};

        {
            let informationAsJSON = CBConvert.valueToString(
                viewElement.dataset.information
            );

            if (informationAsJSON !== "") {
                information = JSON.parse(
                    informationAsJSON
                );
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
                SCCartItem.getQuantity(
                    activeCartItemSpec
                )
            );

            activeCartItemSpec.CBActiveObject.replace(
                originalCartItemSpec
            );
        }

        let viewContentElement = CBUI.createElement(
            "CBUI_viewContent"
        );

        viewElement.appendChild(
            viewContentElement
        );


        /* productPageURL */

        let productPageURL = "";

        let shouldShowProductPageLink = CBModel.valueToBool(
            information,
            "showProductPageLink"
        );

        if (shouldShowProductPageLink) {
            productPageURL = CBModel.valueToString(
                information,
                "productPageURL"
            ).trim();
        }

        /* image */

        let shouldHideImage = CBModel.valueToBool(
            information,
            "hideImage"
        );

        if (!shouldHideImage) {
            let image = CBModel.valueAsModel(
                activeCartItemSpec,
                "image"
            );

            if (image !== undefined) {
                let imageContainerElement = CBUI.createElement(
                    "CBUI_view"
                );

                viewContentElement.appendChild(
                    imageContainerElement
                );

                let artworkElement = CBArtworkElement.create(
                    {
                        aspectRatioWidth: image.width,
                        aspectRatioHeight: image.height,
                        maxHeight: 240,
                        URL: CBImage.toURL(
                            image,
                            "rw1280"
                        ),
                        linkURL: productPageURL,
                    }
                );

                imageContainerElement.appendChild(
                    artworkElement
                );
            }
        }


        /* title */

        let titleElement = CBUI.createElement(
            "SCProductBuyView_title CBUI_textAlign_center"
        );

        viewContentElement.appendChild(
            titleElement
        );

        titleElement.textContent = SCCartItem.getTitle(
            activeCartItemSpec
        );


        /* buy button */

        viewContentElement.appendChild(
            createBuyButtonElement(
                activeCartItemSpec
            )
        );

        /* product page link */

        if (productPageURL !== "") {
            viewContentElement.appendChild(
                createProductPageLinkElement(
                    productPageURL
                )
            );
        }
    }
    /* render() */

})();
