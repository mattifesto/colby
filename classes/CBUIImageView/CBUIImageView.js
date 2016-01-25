"use strict";

var CBUIImageView = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return object
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageView";
        var img = document.createElement("img");

        element.appendChild(img);

        var imageChangedCallback = CBUIImageView.handleImageChanged.bind(undefined, {
            img : img,
            propertyName : args.propertyName,
            spec : args.spec,
        });

        imageChangedCallback();

        return {
            element : element,
            imageChangedCallback : imageChangedCallback,
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
    * @param string args.propertyName
    * @param object args.spec
     *
     * @return undefined
     */
    handleImageChanged : function (args) {
        var image = args.spec[args.propertyName];

        if (image === undefined) {
            args.img.src = undefined;
            args.img.style.display = "none";
        } else {
            args.img.src = CBUIImageView.imageToURL(image);
            args.img.style.display = "block";
        }
    },
};
