"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIImageSizeView */

var CBUIImageSizeView = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return object
     */
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageSizeView";

        var imageChangedCallback = CBUIImageSizeView.handleImageChanged.bind(undefined, {
            element: element,
            propertyName: args.propertyName,
            spec: args.spec,
        });

        imageChangedCallback();

        return {
            element: element,
            imageChangedCallback: imageChangedCallback,
        };
    },


    /**
     * @param Element args.element
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return undefined
     */
    handleImageChanged: function (args) {
        var image = args.spec[args.propertyName];

        if (image === undefined) {
            args.element.textContent = "no image";
        } else {
            var width = (image.width/2) + "pt (" + image.width + "px)";
            var height = (image.height/2) + "pt (" + image.height + "px)";
            args.element.textContent = width + " × " + height;
        }
    },
};
