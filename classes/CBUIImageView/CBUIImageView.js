"use strict";

var CBUIImageView = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return object
     */
    createElement : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageView";
        var img = document.createElement("img");

        element.appendChild(img);

        var updateImageCallback = CBUIImageView.updateImage.bind(undefined, {
            img : img,
        });

        updateImageCallback(args.spec[args.propertyName]);

        return {
            element : element,
            updateImageCallback : updateImageCallback,
        };
    },

    /**
     * @param object image
     *
     * @return string
     */
    imageToURL : function (image) {
        return Colby.dataStoreIDToURI(image.ID) + "/" + image.base + "." + image.extension;
    },

    /**
     * @param Element args.img
     * @param object image
     *
     * @return undefined
     */
    updateImage : function (args, image) {
        if (image === undefined) {
            args.img.src = undefined;
            args.img.style.display = "none";
        } else {
            args.img.src = CBUIImageView.imageToURL(image);
            args.img.style.display = "block";
        }
    },
};
