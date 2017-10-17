"use strict";
/* jshint strict: global */
/* exported CBUIImageChooser */

/**
 * This class provides the user interface for selecting and removing images. To
 * maximize usefulness, longevity, and its functional nature, this class is
 * agnostic as to what happens after an image is selected or removed. It can be
 * used with custom handlers or a standard handler that uploads and image and
 * sets a value to a spec property.
 *
 * Because this control does not upload the image the using code must call
 * setImageURLCallback once an image URL is available for the chosen image.
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
     * @param function args.imageChosenCallback
     * @param function args.imageRemovedCallback
     *
     * @return  {
     *              element: Element
     *              setCaptionCallback: function
     *              setImageURLCallback: function
     *          }
     */
    createFullSizedChooser: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageChooser CBDarkTheme full";
        var imageElement = document.createElement("img");
        imageElement.style.display = "none";
        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.style.display = "none";

        var captionElement = document.createElement("div");
        captionElement.className = "caption";
        captionElement.style.display = "none";

        var commandsElement = document.createElement("div");
        commandsElement.className = "commands";
        var chooseElement = document.createElement("div");
        chooseElement.textContent = "choose";
        var removeElement = document.createElement("div");
        removeElement.style.display = "none";
        removeElement.textContent = "remove";

        function setCaption(caption) {
            caption = String(caption);

            if (caption === "") {
                captionElement.style.display = "none";
            } else {
                captionElement.style.display = "block";
            }

            captionElement.textContent = caption;
        }

        function setImageURI(URI) {
            if (URI) {
                imageElement.src = URI;
                imageElement.style.display = "block";
                removeElement.style.display = "block";
            } else {
                imageElement.src = "";
                imageElement.style.display = "none";
                removeElement.style.display = "none";
            }
        }

        chooseElement.addEventListener("click", function () {
            inputElement.click();
        });

        inputElement.addEventListener("change", function () {
            if (typeof args.imageChosenCallback === "function") {
                args.imageChosenCallback.call(undefined, {
                    file: inputElement.files[0],
                    setCaptionCallback: setCaption,
                    setImageURLCallback: setImageURI,
                });
            }

            inputElement.value = null;
        });

        removeElement.addEventListener("click", function () {
            setImageURI("");
            setCaption("");

            if (typeof args.imageRemovedCallback === "function") {
                args.imageRemovedCallback.call(undefined, {
                    setImageURLCallback: setImageURI,
                });
            }
        });

        element.appendChild(inputElement);
        element.appendChild(imageElement);
        element.appendChild(captionElement);
        commandsElement.appendChild(chooseElement);
        commandsElement.appendChild(removeElement);
        element.appendChild(commandsElement);

        return {
            element: element,
            setCaptionCallback: setCaption,
            setImageURLCallback: setImageURI,
        };
    },

    /**
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
     *          setImageURLCallback: function
     *
     *              deprecated: use setImageURI
     *      }
     */
    createThumbnailSizedChooser : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageChooser CBDarkTheme thumbnail";
        var imageElement = document.createElement("img");
        imageElement.style.display = "none";
        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.style.display = "none";

        var captionElement = document.createElement("div");
        captionElement.className = "caption";
        captionElement.style.display = "none";

        var commandsElement = document.createElement("div");
        commandsElement.className = "commands";
        var chooseElement = document.createElement("div");
        chooseElement.textContent = "choose";
        var removeElement = document.createElement("div");
        removeElement.style.display = "none";
        removeElement.textContent = "remove";

        function setCaption(caption) {
            caption = String(caption);

            if (caption === "") {
                captionElement.style.display = "none";
            } else {
                captionElement.style.display = "block";
            }

            captionElement.textContent = caption;
        }

        function setImageURI(URI) {
            if (URI) {
                imageElement.src = URI;
                imageElement.style.display = "block";
                removeElement.style.display = "block";
            } else {
                imageElement.src = "";
                imageElement.style.display = "none";
                removeElement.style.display = "none";
            }
        }

        chooseElement.addEventListener("click", function () {
            inputElement.click();
        });

        inputElement.addEventListener("change", function() {
            if (typeof args.imageChosenCallback === "function") {
                args.imageChosenCallback.call(undefined, {
                    file: inputElement.files[0],
                    setCaptionCallback: setCaption,
                    setImageURLCallback: setImageURI,
                });
            }

            inputElement.value = null;
        });

        removeElement.addEventListener("click", function () {
            setImageURI("");
            setCaption("");

            if (typeof args.imageRemovedCallback === "function") {
                args.imageRemovedCallback.call(undefined, {
                    setImageURLCallback: setImageURI,
                });
            }
        });

        element.appendChild(inputElement);
        element.appendChild(imageElement);
        element.appendChild(captionElement);
        commandsElement.appendChild(chooseElement);
        commandsElement.appendChild(removeElement);
        element.appendChild(commandsElement);

        return {
            element : element,
            setCaption: setCaption,
            setImageURI: setImageURI,
            setImageURLCallback : setImageURI, /* deprecated */
        };
    },
};
