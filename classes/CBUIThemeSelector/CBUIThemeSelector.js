"use strict";
/* jshint strict: global */
/* exported CBUIThemeSelector */
/* global
    CBUISelector,
    Colby */

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
    create : function (args) {
        var selector = CBUISelector.create({
            labelText : args.labelText,
            navigateToItemCallback : args.navigateToItemCallback,
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        CBUIThemeSelector.fetchThemeOptions({
            classNameForKind : args.classNameForKind,
            updateOptionsCallback : selector.updateOptionsCallback,
        });

        return {
            element : selector.element,
        };
    },

    /**
     * @param string args.classNameForKind
     * @param function args.updateOptionsCallback
     *
     * @return undefined
     */
    fetchThemeOptions : function (args) {
        var data = new FormData();
        data.append("classNameForKind", args.classNameForKind);

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr : xhr});
        xhr.onload = CBUIThemeSelector.handleThemeOptionsDidLoad.bind(undefined, {
            updateOptionsCallback : args.updateOptionsCallback,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBUIThemeSelector&function=fetchThemeOptions");
        xhr.send(data);
    },

    /**
     * @param function args.updateOptionsCallback
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleThemeOptionsDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var options = [{title:"Default", description:"", value:undefined}].concat(response.options);
            args.updateOptionsCallback(options);
        } else {
            Colby.displayResponse(response);
        }
    },
};
