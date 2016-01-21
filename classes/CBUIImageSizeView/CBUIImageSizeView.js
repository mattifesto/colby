"use strict";

var CBUIImageSizeView = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return object
     */
    createElement : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageSizeView";

        var updateImageSizeCallback = CBUIImageSizeView.updateImageSize.bind(undefined, {
            element : element,
        });

        updateImageSizeCallback(args.spec[args.propertyName]);

        return {
            element : element,
            updateImageSizeCallback : updateImageSizeCallback,
        };
    },

    /**
     * @param Element args.element
     * @param object image
     *
     * @return undefined
     */
    updateImageSize : function (args, image) {
        if (image === undefined) {
            args.element.textContent = "no image";
        } else {
            var px = image.width + "px × " + image.height + "px";
            var pt = "(" + (image.width/2) + "pt × " + (image.height/2) + "pt)";
            args.element.textContent = px + " " + pt;
        }
    },
};
