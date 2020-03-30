"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCShoppingCart */
/* global
    CBModel,
    CBModels,
    CBUIPanel,
    Colby,
    SCCartItem,
    SCCartItemCollection,

    SCShoppingCart
*/



(function() {
    let mainShoppingCartID = "a2adb1ceff0339fc399e5816ff163e5b5ca2d627";
    let mainShoppingCartSpec;
    let mainShoppingCartVersion;
    let mainCartItemSpecs;
    let currentSaveSoon;
    let currentSavePromise = init_generatePromise();

    window.SCShoppingCart = {

        adjustMainCartItemQuantity: adjustMainCartItemQuantity,
        getMainCartItemQuantity: getMainCartItemQuantity,
        setMainCartItemQuantity: setMainCartItemQuantity,

        /**
         * @return SCCartItemCollection
         */
        get mainCartItemSpecs() {
            return init_getMainCartItemSpecs();
        },

        /**
         * @return Promise
         */
        get mainCartSavePromise() {
            return currentSavePromise;
        },

        /**
         * @param activeCartItems [object]
         *
         * @return undefined
         */
        empty: init_empty,

    };
    /* window.SCShoppingCart */

    return;



    /* -- closures -- -- -- -- -- */



    /**
     * This function adjusts the quantity of a cart item in the main
     * shopping cart. The promise returned will resolve when the adjustment
     * is complete. If the cart has been adjusted on another page this
     * function will reload it before making the adjustment.
     *
     * @param object cartItem
     * @param number incrementalQuantity
     *
     * @return Promise -> undefined
     */
    function adjustMainCartItemQuantity(
        cartItem,
        incrementalQuantity
    ) {
        return setMainCartItemQuantity(
            cartItem,
            getMainCartItemQuantity(cartItem) + incrementalQuantity
        );
    }
    /* adjustMainCartItemQuantity() */



    /**
     * @param object cartItem
     *
     * @return number
     */
    function getMainCartItemQuantity(
        cartItem
    ) {
        /**
         * @TODO reload cart if cart has been changed in another session
         */

         let activeCartItem =
         SCShoppingCart
         .mainCartItemSpecs
         .fetchCartItem(cartItem);

         return SCCartItem.getQuantity(
             activeCartItem
         );
    }
    /* getMainCartItemQuantity() */



    /**
     * @param object cartItem
     * @param number quantity
     *
     * @return Promise -> undefined
     */
    function setMainCartItemQuantity(
        cartItem,
        quantity
    ) {
        /**
         * @TODO reload cart if cart has been changed in another session
         */

         let activeCartItem =
         SCShoppingCart
         .mainCartItemSpecs
         .fetchCartItem(cartItem);

         let previousQuantity = SCCartItem.getQuantity(
             activeCartItem
         );

         if (previousQuantity === quantity) {
             return Promise.resolve();
         }

         SCCartItem.setQuantity(
             activeCartItem,
             quantity
         );

         activeCartItem
         .CBActiveObject
         .tellListenersThatTheObjectDataHasChanged();

         return SCShoppingCart.mainCartSavePromise;
    }
    /* setMainCartItemQuantity() */



    /**
     * Sets the quantity of all the cart items in the main shopping cart
     * to 0.
     *
     * @param activeCartItems [object]
     *
     *      The default value is the array of cart items in the main
     *      shopping cart.
     *
     * @return undefined
     */
    function init_empty(activeCartItems) {
        if (activeCartItems === undefined) {
            activeCartItems =
            SCShoppingCart.mainCartItemSpecs.getCartItems();
        }

        activeCartItems.forEach(
            function (currentCartItem) {
                SCCartItem.setQuantity(currentCartItem, 0);

                currentCartItem
                .CBActiveObject
                .tellListenersThatTheObjectDataHasChanged();
            }
        );
    }



    /**
     * @return SCCartItemCollection
     */
    function init_getMainCartItemSpecs() {
        if (mainShoppingCartSpec === undefined) {
            let record = CBModels.fetch(
                mainShoppingCartID,
                localStorage
            );

            if (record === undefined) {
                mainShoppingCartSpec = {};
                mainShoppingCartVersion = 0;
            } else {
                mainShoppingCartSpec = record.spec;
                mainShoppingCartVersion = record.meta.version;
            }

            CBModel.merge(
                mainShoppingCartSpec,
                {
                    className: "SCShoppingCart",
                }
            );

            let originalCartItems =
            SCCartItem.cleanAndConsolidateCartItems(
                CBModel.valueToArray(
                    mainShoppingCartSpec,
                    "cartItems"
                )
            );

            mainCartItemSpecs = SCCartItemCollection.create();

            mainCartItemSpecs.replaceCartItems(originalCartItems);

            mainCartItemSpecs.addEventListener(
                "somethingChanged",
                function handleSomethingChanged() {
                    currentSaveSoon();
                }
            );
        }

        return mainCartItemSpecs;
    }
    /* init_getMainCartItemSpecs() */



    /**
     * @return Promise
     */
    function init_generatePromise() {
        return new Promise(
            function (resolve, reject) {
                return init_initializePromise(resolve, reject);
            }
        ).catch(
            function (error) {
                return init_handleError(error);
            }
        );
    }
    /* init_generatePromise() */



    /**
     * @param Error error
     *
     * @return undefined
     */
    function init_handleError(
        error
    ) {
        let savedFromAnotherSessionErrorSourceCBID = (
            "1d094e7ef6db1efc327c1b8addd0c7ec758dccd9"
        );

        let cartWasSavedFromAnotherSession = (
            error.CBException &&
            (
                error.CBException.sourceID ===
                savedFromAnotherSessionErrorSourceCBID
            )
        );

        let cbmessage;

        if (cartWasSavedFromAnotherSession) {
            cbmessage = `

                Your recent updates to the shopping cart have not been saved
                because other changes were made and saved to the shopping cart
                from another window or tab. Reload this page and make your
                changes again.

            `;
        } else {
            cbmessage = `

                An error occurred when trying to save the recent changes to your
                shopping cart. Reload this page and make your changes again.

            `;
        }

        if (!cartWasSavedFromAnotherSession) {
            Colby.reportError(error);
        }

        /**
         * @NOTE 2019_05_07
         *
         *      I don't love displaying UI from this class, but it's a first
         *      step forward toward actually updating the cart if the cart
         *      has been changed on another page. Also, the developer should
         *      be able to customize this UI.
         *
         * @NOTE 2020_02_16
         *
         *      Displaying the panel can be technically required before
         *      DOMContentLoaded although that is extemely unlikely. Wrapping
         *      CBUIPanel.displayCBMessage() inside
         *      Colby.afterDOMContentLoaded() will avoid errors that would occur
         *      if an error were to happen at that time.
         */

        Colby.afterDOMContentLoaded(
            function () {
                CBUIPanel.displayCBMessage(
                    cbmessage,
                    "Reload this Page"
                ).then(
                    function () {
                        window.location.reload();
                    }
                );
            }
        );
    }
    /* init_handleError() */



    /**
     * @param function(value) resolve
     * @param function(error) reject
     *
     * @return undefined
     */
    function init_initializePromise(resolve, reject) {
        let timeoutID;

        currentSaveSoon = init_initializePromise_saveSoon;

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function init_initializePromise_saveSoon() {
            try {
                if (timeoutID) {
                    clearTimeout(timeoutID);
                }

                /**
                 * @NOTE 2019_06_03
                 *
                 *      If the user navigates before save() executes the
                 *      user's changes will be lost. The eventual solution
                 *      to this is to make sure all cart operations are
                 *      persistent across windows and navigations.
                 *
                 *      We used to try to add an event listener for
                 *      navigation events here but that is unreliable and
                 *      always will be.
                 */

                timeoutID = setTimeout(init_initializePromise_saveNow, 0);
            } catch (error) {
                reject(error);
            }
        }
        /* init_initializePromise_saveSoon() */



        /**
         * @return undefined
         */
        function init_initializePromise_saveNow() {
            try {
                timeoutID = undefined;

                mainShoppingCartSpec.cartItems =
                mainCartItemSpecs.getCartItems();

                CBModels.save(
                    mainShoppingCartID,
                    mainShoppingCartSpec,
                    mainShoppingCartVersion,
                    localStorage
                );

                mainShoppingCartVersion += 1;

                resolve();
            } catch (error) {
                reject(error);
            }

            currentSavePromise = init_generatePromise();
        }
        /* init_initializePromise_saveNow() */
    }
    /* init_initializePromise() */

})();
