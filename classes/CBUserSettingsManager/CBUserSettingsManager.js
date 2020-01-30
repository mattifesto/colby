"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBModel,
*/



(function () {

        window.CBUserSettingsManager = {
            createElement,
        };



        /**
         * @param object args
         *
         *      {
         *          className: string
         *          targetUserCBID: CBID
         *      }
         */
        function createElement(args) {
            let callable = CBModel.valueAsFunction(
                window[args.className],
                "CBUserSettingsManager_createElement"
            );

            if (callable === undefined) {
                throw Error("interface not found");
            }

            return callable(
                {
                    targetUserCBID: args.targetUserCBID,
                }
            );
        }
        /* createElement() */

})();
