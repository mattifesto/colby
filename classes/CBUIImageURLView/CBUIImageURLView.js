"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIImageURLView */

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
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageURLView";
        var img = document.createElement("img");

        element.appendChild(img);

        var imageChangedCallback = CBUIImageURLView.handleImageChanged.bind(undefined, {
            img: img,
            propertyName: args.propertyName,
            spec: args.spec,
        });

        imageChangedCallback();

        element.addEventListener("click", CBUIImageURLView.rotateClass.bind(undefined, {
            element: element,
        }));

        return {
            element: element,
            imageChangedCallback: imageChangedCallback,
        };
    },


    /**
     * @param Element args.img
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return undefined
     */
    handleImageChanged: function (args) {
        var URL = args.spec[args.propertyName];

        if (URL === undefined) {
            args.img.src = undefined;
            args.img.style.display = "none";
        } else {
            args.img.src = URL;
            args.img.style.display = "block";
        }
    },


    /**
     * @param args.element
     *
     * @return undefined
     */
    rotateClass: function (args) {
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
