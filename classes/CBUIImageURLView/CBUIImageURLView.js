"use strict";

var CBUIImageURLView = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return {
     *  Element element,
     *  function imageChangedCallback,
     * }
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageURLView";
        var img = document.createElement("img");

        element.appendChild(img);

        var imageChangedCallback = CBUIImageURLView.handleImageChanged.bind(undefined, {
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
     * @param Element args.img
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return undefined
     */
    handleImageChanged : function (args) {
        var URL = args.spec[args.propertyName];

        if (URL === undefined) {
            args.img.src = undefined;
            args.img.style.display = "none";
        } else {
            args.img.src = URL;
            args.img.style.display = "block";
        }
    },
};
