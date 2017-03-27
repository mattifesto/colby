"use strict"; /* jshint strict: global */
/* globals
    Colby */

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

        element.addEventListener("click", CBUIImageView.rotateClass.bind(undefined, {
            element : element,
        }));

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
        return Colby.dataStoreIDToURI(image.ID) + "/rw960." + image.extension;
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
            args.img.src = "";
            args.img.style.display = "none";
        } else {
            args.img.src = CBUIImageView.imageToURL(image);
            args.img.style.display = "block";
        }
    },

    /**
     * @param args.element
     *
     * @return undefined
     */
    rotateClass : function (args) {
        var classList = args.element.classList;

        if (classList.contains("medium")) {
            classList.remove("medium");
            classList.add("dark");
        } else if (classList.contains("dark")) {
            classList.remove("dark");
        } else {
            classList.add("medium");
        }
    },
};
