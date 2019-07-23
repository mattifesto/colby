"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIThemeSelector */
/* global
    CBUISelector,
    Colby,
*/

var CBUIThemeSelector = {

    /**
     * @param string args.classNameForKind
     * @param string args.labelText
     * @param function args.navigateToItemCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return object
     */
    create: function (args) {
        var selector = CBUISelector.create(
            {
                labelText: args.labelText,
                navigateToItemCallback: args.navigateToItemCallback,
                propertyName: args.propertyName,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }
        );

        Colby.callAjaxFunction(
            "CBUIThemeSelector",
            "fetchThemeOptions",
            {
                classNameForKind: args.classNameForKind,
            }
        ).then(
            function (options) {
                selector.options = options;
            }
        ).catch(
            function (error) {
                Colby.displayAndReportError(error);
            }
        );

        return {
            element: selector.element,
        };
    },
    /* create() */
};
