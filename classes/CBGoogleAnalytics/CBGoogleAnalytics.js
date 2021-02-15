/* global
    gtag,
*/

(function () {
    "use strict";

    window.CBGoogleAnalytics = {
        sendViewItemEvent: CBGoogleAnalytics_sendViewItemEvent,
        sendPurchaseEvent: CBGoogleAnalytics_sendPurchaseEvent,
    };


    function
    CBGoogleAnalytics_sendPurchaseEvent(

    ) {
        if (typeof gtag !== "function") {
            return;
        }

        gtag(
            'event',
            'purchase',
            {
                "transaction_id": "24.031608523954162",
                "affiliation": "Google online store",
                "value": 23.07,
                "currency": "USD",
                "tax": 1.24,
                "shipping": 0,
                "items": [
                    {
                        "id": "P12345",
                        "name": "Android Warhol T-Shirt",
                        "list_name": "Search Results",
                        "brand": "Google",
                        "category": "Apparel/T-Shirts",
                        "variant": "Black",
                        "list_position": 1,
                        "quantity": 2,
                        "price": '2.0'
                    },
                    {
                        "id": "P67890",
                        "name": "Flame challenge TShirt",
                        "list_name": "Search Results",
                        "brand": "MyBrand",
                        "category": "Apparel/T-Shirts",
                        "variant": "Red",
                        "list_position": 2,
                        "quantity": 1,
                        "price": '3.0'
                    }
                ]
            }
        );
    }
    /* CBGoogleAnalytics_sendPurchaseEvent() */



    /**
     * @return undefined
     */
    function
    CBGoogleAnalytics_sendViewItemEvent(
    ) {
        if (typeof gtag !== "function") {
            return;
        }

        gtag(
            "event",
            "view_item",
            {
                "items": [
                    {
                        "id": "P12345",
                        "name": "Android Warhol T-Shirt",
                        "list_name": "Search Results",
                        "brand": "Google",
                        "category": "Apparel/T-Shirts",
                        "variant": "Black",
                        "list_position": 1,
                        "quantity": 2,
                        "price": "2.0"
                    }
                ]
            }
        );
    }
    /* CBGoogleAnalytics_sendViewItemEvent() */

})();
