"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIImageChooser */

/**
 * This class provides the user interface for selecting and removing images. To
 * maximize usefulness, longevity, and its functional nature, this class is
 * agnostic as to what happens after an image is selected or removed. It can be
 * used with custom handlers or a standard handler that uploads and image and
 * sets a value to a spec property.
 *
 * Because this control does not upload the image the using code must call
 * setImageURI once an image URL is available for the chosen image.
 *
 * Instructions:
 *
 *  - The imageChosenCallback is generally custom written for each use of this
 *    class. In it you will generally call one or more Ajax functions to upload
 *    and possibly resize the selected image.
 *
 *  - The imageRemovedCallback should generally just unset the spec variables
 *    related to the image.
 */
var CBUIImageChooser = {

    /**
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *          caption: string (get, set)
     *          chosen: function (get, set)
     *          removed: function (get, set)
     *          src: string (get, set)
     *      }
     */
    create: function () {
        var chosen, removed;

        var element = document.createElement("div");
        element.className = "CBUIImageChooser CBDarkTheme";

        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.style.display = "none";

        element.appendChild(inputElement);

        var imageContainerElement = document.createElement("div");
        imageContainerElement.className = "container";
        var imageElement = document.createElement("img");
        imageElement.style.display = "none";

        imageContainerElement.appendChild(imageElement);
        element.appendChild(imageContainerElement);

        var captionElement = document.createElement("div");
        captionElement.className = "caption";
        captionElement.style.display = "none";

        element.appendChild(captionElement);

        var commandsElement = document.createElement("div");
        commandsElement.className = "commands";

        var chooseElement = document.createElement("div");
        chooseElement.textContent = "choose";

        chooseElement.addEventListener("click", function () {
            inputElement.click();
        });

        inputElement.addEventListener("change", function() {
            if (typeof chosen === "function") {
                chosen({
                    file: inputElement.files[0],
                    setCaptionCallback: function (value) { obj.caption = value; }, /* deprecated */
                    setImageURI: function (value) { obj.src = value; }, /* deprecated */
                    setImageURLCallback: function (value) { obj.src = value; }, /* deprecated */
                });
            }

            inputElement.value = null;
        });

        commandsElement.appendChild(chooseElement);

        var removeElement = document.createElement("div");
        removeElement.style.display = "none";
        removeElement.textContent = "remove";

        removeElement.addEventListener("click", function () {
            obj.caption = "";
            obj.src = "";

            if (typeof removed === "function") {
                removed({
                    setImageURI: function (value) { /* deprecated */
                        obj.src = value;
                    },
                    setImageURLCallback: function (value) { /* deprecated */
                        obj.src = value;
                    },
                });
            }
        });

        commandsElement.appendChild(removeElement);
        element.appendChild(commandsElement);

        let obj = {
            get caption() {
                return captionElement.textContent;
            },
            set caption(value) {
                let caption = String(value);

                if (caption === "") {
                    captionElement.style.display = "none";
                } else {
                    captionElement.style.display = "block";
                }

                captionElement.textContent = caption;
            },
            get chosen() {
                return chosen;
            },
            set chosen(value) {
                chosen = value;
            },
            get element() {
                return element;
            },
            get removed() {
                return removed;
            },
            set removed(value) {
                removed = value;
            },
            get src() {
                return imageElement.src;
            },
            set src(value) {
                imageElement.src = value;

                if (value) {
                    imageElement.style.display = "block";
                    removeElement.style.display = "block";
                } else {
                    imageElement.style.display = "none";
                    removeElement.style.display = "none";
                }
            },
        };

        return obj;
    },

    /**
     * @deprecated use create()
     *
     * @param object args
     *
     *      {
     *          imageChosenCallback: function
     *          imageRemovedCallback: function
     *      }
     *
     * @return  object
     *
     *      {
     *          element: Element
     *          setCaptionCallback: function
     *          setImageURI: function
     *          setImageURLCallback: function (deprecated)
     *      }
     */
    createFullSizedChooser: function (args) {
        let chooser = CBUIImageChooser.create();
        chooser.element.classList.add("full");

        chooser.chosen = args.imageChosenCallback;
        chooser.removed = args.imageRemovedCallback;

        chooser.setCaptionCallback = function (value) { chooser.caption = value; }; /* deprecated */
        chooser.setImageURI = function (value) { chooser.src = value; }; /* deprecated */
        chooser.setImageURLCallback = function (value) { chooser.src = value; }; /* deprecated */

        return chooser;
    },

    /**
     * @deprecated use create()
     *
     * @param object args
     *
     *      {
     *          imageChosenCallback: function
     *          imageRemovedCallback: function
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *          setCaption: function,
     *          setImageURI: function,
     *          setImageURLCallback: function (deprecated)
     *      }
     */
    createThumbnailSizedChooser : function (args) {
        let chooser = CBUIImageChooser.create();
        chooser.element.classList.add("thumbnail");

        chooser.chosen = args.imageChosenCallback;
        chooser.removed = args.imageRemovedCallback;

        chooser.setCaptionCallback = function (value) { chooser.caption = value; }; /* deprecated */
        chooser.setImageURI = function (value) { chooser.src = value; }; /* deprecated */
        chooser.setImageURLCallback = function (value) { chooser.src = value; }; /* deprecated */

        return chooser;
    },
};
