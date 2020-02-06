"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBException,
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
            let className = args.className;
            let functionName = "CBUserSettingsManager_createElement";
            let callable = CBModel.valueAsFunction(
                window[className],
                functionName
            );

            if (callable === undefined) {
                throw CBException.withError(
                    Error(
                        `The ${functionName}() interface has not been ` +
                        `implemented on the ${className} object.`
                    ),
                    "",
                    "c3c53541780510f2861550627ac00439d748291e"
                );
            }

            return callable(
                {
                    targetUserCBID: args.targetUserCBID,
                }
            );
        }
        /* createElement() */

})();
