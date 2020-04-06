"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCStripe */
/* global
    CBModel,
    SCStripe_apiVersion,
*/

var SCStripe = {

    /**
     * @param object args
     *
     *      {
     *          apiURL: string
     *          apiKey: string
     *          apiArgs: object
     *      }
     *
     * @return Promise -> object
     */
    call: function (args) {
        return new Promise(function (resolve, reject) {
            let URL = CBModel.valueToString(args, "apiURL");
            let key = CBModel.valueToString(args, "apiKey");

            let xhr = new XMLHttpRequest();
            xhr.onloadend = onLoadEnd;
            xhr.open("POST", URL);
            xhr.setRequestHeader("Authorization", "BEARER " + key);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=UTF-8");
            xhr.setRequestHeader("Stripe-Version", SCStripe_apiVersion);

            let apiArgs = CBModel.valueToObject(args, "apiArgs");
            let urlEncodedAPIArgs = [];

            let keys = Object.keys(apiArgs);
            for (let i = 0; i < keys.length; i += 1) {
                let key = keys[i];
                let value = apiArgs[key];
                let encodedKey = encodeURIComponent(key);
                let encodedValue = encodeURIComponent(value);
                urlEncodedAPIArgs.push(encodedKey + "=" + encodedValue);
            }

            xhr.send(urlEncodedAPIArgs.join("&"));

            /**
             * closure
             *
             * @return undefined
             */
            function onLoadEnd() {
                if (xhr.status === 200) {
                    resolve(JSON.parse(xhr.responseText));
                } else {

                    /**
                     * @NOTE 2018_10_09
                     *
                     *      We are discarding the error object returned by
                     *      Stripe. It might be good to find a way to make this
                     *      object available to callers.
                     *
                     *      Need to test with non-Stripe errors, such as network
                     *      errors.
                     */

                    let response = JSON.parse(xhr.responseText);
                    let message = CBModel.valueToString(response, "error.message");

                    reject(new Error(message));
                }
            }
        });
    },
};
