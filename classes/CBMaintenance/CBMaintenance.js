"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMaintenance */
/* global
    Colby,
*/

var CBMaintenance = {

    /**
     * @param string title
     * @param function callback
     *
     * @return Promise
     */
    transaction: function(title, callback) {
        let locked = false;
        let api;

        return CBMaintenance.lock(title).then(
            function (value) {
                locked = true;
                api = value;
            }
        ).then(
            callback
        ).finally(
            function () {
                if (locked) {
                    api.unlock();
                }
            }
        );
    },

    /**
     * @param string title
     *
     * @return Promise -> object
     *
     *      {
     *          unlock()
     *      }
     */
    lock: function (title) {
        let holderID = Colby.random160();
        let active = true;

        return go().then(
            function () {
                return {
                    unlock: unlock,
                };
            }
        );

        /**
         * CBMaintenance.lock() closure
         *
         * @return Promise
         */
        function go() {
            if (active) {
                return Colby.callAjaxFunction(
                    "CBMaintenance",
                    "lock",
                    {
                        holderID: holderID,
                        title: title,
                    }
                ).then(
                    function () {
                        setTimeout(go, 20000);
                    }
                );
            }
        }

        /**
         * CBMaintenance.lock() closure
         *
         * @return Promise
         */
        function unlock() {
            active = false;

            return Colby.callAjaxFunction(
                "CBMaintenance",
                "unlock",
                {
                    holderID: holderID,
                }
            );
        }
    },
};
