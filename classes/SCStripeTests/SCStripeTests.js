"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCStripeTests */
/* globals
    CBConvert,
    CBMessageMarkup,
    CBModel,
    SCStripe,
    SCStripe_testPublishableKey,
*/

var SCStripeTests = {

    /**
     * @return object | Promise
     */
    CBTest_tokens: function () {
        if (SCStripe_testPublishableKey === "") {
            return {
                succeeded: true,
                message: 'Tests not run because there a no Stripe API keys set.'
            };
        }

        return SCStripe.call({
            apiURL: "https://api.stripe.com/v1/tokens",
            apiKey: SCStripe_testPublishableKey,
            apiArgs: {
                "card[number]": " 4242 4242 4242 4242 ",
                "card[exp_month]": "12",
                "card[exp_year]": "2020",
                "card[cvc]": "123",
            },
        }).then(function (response) {
            let id = CBModel.valueToString(response, 'id');

            if (id.match(/^tok_/)) {
                return {
                    succeeded: true
                };
            } else {
                let responseAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(response)
                );

                let message = `

                    Unable to find a valid token in the Stripe response:

                    --- pre\n${responseAsMessage}
                    ---

                `;

                return {
                    succeeded: false,
                    message: message,
                };
            }
        });
    },
};
