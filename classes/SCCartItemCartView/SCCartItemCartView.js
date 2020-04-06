"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItemCartView */
/* global
    CBArtworkElement,
    CBConvert,
    CBImage,
    CBMessageMarkup,
    CBModel,
    CBReleasable,
    CBUI,
    SCCartItem,
*/

var SCCartItemCartView = {

    /* -- functions -- -- -- -- -- */



    /**
     * @param object cartItemSpec
     *
     * @return Element
     */
    createElement: function (originalActiveCartItemSpec) {
        let cachedClassName;
        let cartItemCartViewElement;

        let containerElement = CBUI.createElement(
            "SCCartItemCartView_container"
        );

        originalActiveCartItemSpec.CBActiveObject.addEventListener(
            "theObjectDataHasChanged",
            theObjectDataHasChanged
        );

        originalActiveCartItemSpec.CBActiveObject.addEventListener(
            "theObjectHasBeenReplaced",
            theObjectHasBeenReplaced
        );

        originalActiveCartItemSpec.CBActiveObject.addEventListener(
            "theObjectHasBeenDeactivated",
            theObjectHasBeenDeactivated
        );

        let activeCartItemSpec = originalActiveCartItemSpec;
        originalActiveCartItemSpec = undefined;

        theObjectDataHasChanged();

        return containerElement;



        /* -- closures -- -- -- -- -- */



        /**
         * closure in createElement()
         *
         * return Element
         */
        function createCartItemCartViewElement() {
            let cartItemCartViewClassName = CBModel.valueToString(
                activeCartItemSpec,
                "className"
            ) + "CartView";

            let callable = CBConvert.valueAsFunction(
                CBModel.value(
                    window,
                    (
                        cartItemCartViewClassName +
                        ".SCCartItemCartView_createElement"
                    )
                )
            );

            if (callable !== undefined) {
                return callable(activeCartItemSpec);
            } else {
                return SCCartItemCartView.createDefaultCartItemCartViewElement(
                    activeCartItemSpec
                );
            }
        }



        /**
         * closure in createElement()
         *
         * @return undefined
         */
        function theObjectDataHasChanged() {
            if (
                cartItemCartViewElement === undefined ||
                activeCartItemSpec.className !== cachedClassName
            ) {
                if (cartItemCartViewElement) {
                    containerElement.removeChild(cartItemCartViewElement);

                    cartItemCartViewElement.CBReleasable.release();
                }

                cartItemCartViewElement = createCartItemCartViewElement(
                    activeCartItemSpec
                );

                CBReleasable.assertObjectIsReleasable(cartItemCartViewElement);

                containerElement.appendChild(cartItemCartViewElement);

                cachedClassName = activeCartItemSpec.className;
            }

            let isNotAvailable = SCCartItem.getIsNotAvailable(
                activeCartItemSpec
            );

            if (isNotAvailable) {
                containerElement.classList.add(
                    "SCCartItemCartView_itemIsNotAvalailable"
                );

                containerElement.classList.remove(
                    "SCCartItemCartView_itemIsAvailable"
                );
            } else {
                containerElement.classList.add(
                    "SCCartItemCartView_itemIsAvailable"
                );

                containerElement.classList.remove(
                    "SCCartItemCartView_itemIsNotAvalailable"
                );
            }
        }
        /* theObjectDataHasChanged() */



        /**
         * closure in createElement()
         *
         * @return undefined
         */
        function theObjectHasBeenReplaced(replacementObject) {
            activeCartItemSpec = replacementObject;

            theObjectDataHasChanged();
        }



        /**
         * closure in createElement()
         *
         * @return undefined
         */
        function theObjectHasBeenDeactivated() {
            if (containerElement) {
                containerElement.removeChild(cartItemCartViewElement);
                cartItemCartViewElement.CBReleasable.release();
            }
        }

    },
    /* createElement() */



    /**
     * @param object cartItemSpec
     *
     * @return Element
     */
    createDefaultCartItemCartViewElement: function (
        cartItemSpec
    ) {
        let element = CBUI.createElement(
            "SCCartItemCartView_defaultCartItemCartView CBUI_sectionContainer"
        );

        CBReleasable.activate(
            element,
            function () {
                quantityTitleAndPriceElement.CBReleasable.release();
            }
        );


        /* section element */

        let sectionElement = CBUI.createElement("CBUI_section");

        element.appendChild(sectionElement);


        /* quantity, title, and price section item element */

        let quantityTitleAndPriceElement = SCCartItemCartView.
        cartItemSpecToQuantityTitleAndPriceSectionItemElement(
            cartItemSpec
        );

        sectionElement.appendChild(
            quantityTitleAndPriceElement
        );


        /* image section item element */

        sectionElement.appendChild(
            SCCartItemCartView.cartItemSpecToImageSectionItemElement(
                cartItemSpec
            )
        );


        /* message section item element */

        sectionElement.appendChild(
            SCCartItemCartView.cartItemSpecToMessageSectionItemElement(
                cartItemSpec
            )
        );


        /* increase quantity button */
        {
            let actionElement = CBUI.createElement(
                "CBUI_action SCCartItemCartView_increaseQuantityButton"
            );

            sectionElement.appendChild(actionElement);

            actionElement.textContent = "Increase Quantity";

            actionElement.addEventListener(
                "click",
                function () {
                    let startingQuantity = SCCartItem.getQuantity(
                        cartItemSpec
                    );

                    SCCartItem.setQuantity(
                        cartItemSpec,
                        startingQuantity + 1
                    );

                    let endingQuantity = SCCartItem.getQuantity(
                        cartItemSpec
                    );

                    if (startingQuantity === endingQuantity) {
                        return;
                    }

                    /**
                     * @TODO 2019_04_17
                     *
                     *      The following code makes assumptions about
                     *      unitPriceInCents and priceInCents to temporarily
                     *      improve the shopping cart page user interface.
                     *      Changes to prices should only happen on the server.
                     *      When we send the cart item spec to the server to be
                     *      updated, we can remove the code altering prices.
                     */

                    let unitPriceInCents = CBModel.valueAsInt(
                        cartItemSpec,
                        "unitPriceInCents"
                    ) || 0;

                    cartItemSpec.priceInCents = (
                        unitPriceInCents * endingQuantity
                    );

                    cartItemSpec
                    .CBActiveObject
                    .tellListenersThatTheObjectDataHasChanged();
                }
            );
        }
        /* increase quantity button */


        /* decrease quantity button */
        {
            let actionElement = CBUI.createElement(
                "CBUI_action SCCartItemCartView_decreaseQuantityButton"
            );

            sectionElement.appendChild(actionElement);

            actionElement.textContent = "Decrease Quantity";

            actionElement.addEventListener(
                "click",
                function () {
                    let startingQuantity = SCCartItem.getQuantity(
                        cartItemSpec
                    );

                    SCCartItem.setQuantity(
                        cartItemSpec,
                        startingQuantity - 1
                    );

                    let endingQuantity = SCCartItem.getQuantity(
                        cartItemSpec
                    );

                    if (startingQuantity === endingQuantity) {
                        return;
                    }

                    /**
                     * @TODO 2019_04_17
                     *
                     *      The following code makes assumptions about
                     *      unitPriceInCents and priceInCents to temporarily
                     *      improve the shopping cart page user interface.
                     *      Changes to prices should only happen on the server.
                     *      When we send the cart item spec to the server to be
                     *      updated, we can remove the code altering prices.
                     */

                    let unitPriceInCents = CBModel.valueAsInt(
                        cartItemSpec,
                        "unitPriceInCents"
                    ) || 0;

                    cartItemSpec.priceInCents = (
                        unitPriceInCents * endingQuantity
                    );

                    cartItemSpec
                    .CBActiveObject
                    .tellListenersThatTheObjectDataHasChanged();
                }
            );
        }
        /* decrease quantity button */


        /* remove button */
        {
            let actionElement = CBUI.createElement(
                "SCCartItemCartView_removeCartItemButton CBUI_action"
            );

            sectionElement.appendChild(actionElement);

            actionElement.textContent = "Remove";

            actionElement.addEventListener(
                "click",
                function () {
                    cartItemSpec.CBActiveObject.deactivate();
                }
            );
        }
        /* remove button */

        return element;
    },
    /* createDefaultCartItemCartViewElement() */



    /**
     * @param object cartItemSpec
     *
     * @return Element
     */
    cartItemSpecToImageSectionItemElement: function (
        cartItemSpec
    ) {
        let aspectRatioWidth = 16;
        let aspectRatioHeight = 9;
        let imageURL = "";

        let image = CBModel.valueAsObject(
            cartItemSpec,
            "image"
        );

        if (image === undefined) {
            imageURL = CBModel.valueToString(
                cartItemSpec,
                "imageURL"
            );
        } else {
            aspectRatioWidth = CBModel.valueAsInt(
                image,
                "width"
            ) || 16;

            aspectRatioHeight = CBModel.valueAsInt(
                image,
                "height"
            ) || 9;

            imageURL = CBImage.toURL(
                image,
                "rw640"
            );
        }

        let sectionItemElement = CBUI.createElement(
            [
                "SCCartItemCartView_imageSectionItem",
                "CBUI_container1",
                "CBUI_content"
            ].join(" ")
        );

        if (imageURL !== "") {
            let artworkElement = CBArtworkElement.create(
                {
                    aspectRatioWidth: aspectRatioWidth,
                    aspectRatioHeight: aspectRatioHeight,
                    URL: imageURL,
                    maxHeight: 160,
                }
            );

            artworkElement.classList.add("CBBackgroundOffsetColor");

            sectionItemElement.appendChild(artworkElement);
        } else {
            sectionItemElement.style.display = "none";
        }

        return sectionItemElement;
    },
    /* cartItemSpecToImageSectionItemElement() */



    /**
     * @param object cartItemModel
     *
     * @return Element
     */
    cartItemSpecToMessageSectionItemElement: function (
        cartItemModel
    ) {
        let sectionItemElement = CBUI.createElement(
            [
                "SCCartItemCartView_messageSectionItem",
                "CBUI_content",
                "CBContentStyleSheet",
            ].join(" ")
        );

        let messageHTML = CBMessageMarkup.messageToHTML(
            CBModel.valueToString(
                cartItemModel,
                "message"
            )
        );

        if (messageHTML !== "") {
            sectionItemElement.innerHTML = messageHTML;
        } else {
            sectionItemElement.style.display = "none";
        }

        return sectionItemElement;
    },
    /* cartItemSpecToMessageSectionItemElement() */



    /**
     * @param object cartItemModel
     *
     * @return Element
     */
    cartItemSpecToQuantityTitleAndPriceSectionItemElement: function (
        cartItemModel
    ) {
        let sectionItemElement = CBUI.createElement(
            "CBUI_sectionItem CBUI_sectionItem_separated"
        );

        if (cartItemModel.CBActiveObject) {
            cartItemModel.CBActiveObject.addEventListener(
                "theObjectDataHasChanged",
                theObjectDataHasChanged
            );

            cartItemModel.CBActiveObject.addEventListener(
                "theObjectHasBeenReplaced",
                theObjectHasBeenReplaced
            );

            CBReleasable.activate(
                sectionItemElement,
                function () {
                    if (cartItemModel.CBActiveObject) {
                        cartItemModel.CBActiveObject.removeEventListener(
                            "theObjectDataHasChanged",
                            theObjectDataHasChanged
                        );

                        cartItemModel.CBActiveObject.removeEventListener(
                            "theObjectHasBeenReplaced",
                            theObjectHasBeenReplaced
                        );
                    }
                }
            );
        }

        /* quantity */

        let quantityElement = document.createElement("div");

        {
            let quantitySectionItemPartElement = CBUI.createElement(
                [
                    "CBUI_sectionItemPart_strings",
                    "CBUI_textAlign_center",
                    "CBUI_flexNone",
                ].join(" ")
            );

            sectionItemElement.appendChild(
                quantitySectionItemPartElement
            );

            let quantityTitleElement = CBUI.createElement(
                "CBUI_sectionItemPart_strings_miniTitle"
            );

            quantityTitleElement.textContent = "Quantity";

            quantitySectionItemPartElement.appendChild(
                quantityTitleElement
            );

            quantitySectionItemPartElement.appendChild(
                quantityElement
            );
        }

        /* title */

        {
            let sourceURL = SCCartItem.getSourceURL(cartItemModel);
            let titleSectionItemPartElement;

            if (sourceURL === "") {
                titleSectionItemPartElement = document.createElement("div");
            } else {
                titleSectionItemPartElement = document.createElement("a");
                titleSectionItemPartElement.href = sourceURL;
            }

            titleSectionItemPartElement.className = [
                "CBUI_sectionItemPart_strings",
                "CBUI_flexGrow",
            ].join(" ");

            sectionItemElement.appendChild(titleSectionItemPartElement);

            let titleElement = CBUI.createElement();
            titleElement.textContent = SCCartItem.getTitle(cartItemModel);

            titleSectionItemPartElement.appendChild(titleElement);
        }

        /* subtotal */

        let subtotalElement = document.createElement("div");

        {
            let subtotalSectionItemPartElement = CBUI.createElement(
                [
                    "CBUI_sectionItemPart_strings",
                    "CBUI_textAlign_center",
                    "CBUI_flexNone",
                ].join(" ")
            );

            sectionItemElement.appendChild(
                subtotalSectionItemPartElement
            );

            let subtotalTitleElement = CBUI.createElement(
                "CBUI_sectionItemPart_strings_miniTitle"
            );

            subtotalTitleElement.textContent = "Subtotal";

            subtotalSectionItemPartElement.appendChild(
                subtotalTitleElement
            );

            subtotalSectionItemPartElement.appendChild(subtotalElement);
        }

        theObjectDataHasChanged();

        return sectionItemElement;

        /* -- closures -- -- -- -- -- */

        /**
         * closure in cartItemSpecToQuantityTitleAndPriceSectionItemElement()
         *
         * @return undefined
         */
        function theObjectDataHasChanged() {
            quantityElement.textContent = SCCartItem.getQuantity(
                cartItemModel
            );

            subtotalElement.textContent = (
                "$" +
                CBConvert.centsToDollars(
                    SCCartItem.getPriceInCents(cartItemModel)
                )
            );
        }

        /**
         * closure in cartItemSpecToQuantityTitleAndPriceSectionItemElement()
         *
         * @param object replacementObject
         *
         * @return undefined
         */
        function theObjectHasBeenReplaced(replacementObject) {
            cartItemModel = replacementObject;
        }
    },
    /* cartItemSpecToQuantityTitleAndPriceSectionItemElement() */

};
/* SCCartItemCartView */
