"use strict"; /* jshint strict: global */

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
     * @return {
     *  Element element
     *  function setImageURLCallback
     * }
     */
    createThumbnailSizedChooser : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIImageChooser thumbnail";
        var buttonElement = document.createElement("div");
        buttonElement.className = "button";
        var plusElement = document.createElement("div");
        plusElement.className = "plus";
        plusElement.textContent = "+";
        var imageElement = document.createElement("img");
        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";

        var setImageURLCallback = CBUIImageChooser.setImageURL.bind(undefined, {
            buttonElement : buttonElement,
            imageElement : imageElement,
        });

        buttonElement.appendChild(plusElement);
        buttonElement.appendChild(imageElement);

        if (typeof args.imageRemovedCallback === "function") {
            var removeElement = document.createElement("div");
            removeElement.className = "remove";
            removeElement.textContent = "×";

            removeElement.addEventListener("click", CBUIImageChooser.handleRemoveElementClicked.bind(undefined, {
                setImageURLCallback : setImageURLCallback,
                imageRemovedCallback : args.imageRemovedCallback,
            }));

            buttonElement.appendChild(removeElement);
        }

        element.appendChild(input);
        element.appendChild(buttonElement);

        buttonElement.addEventListener("click", input.click.bind(input));
        input.addEventListener("change", CBUIImageChooser.handleImageFileChosen.bind(undefined, {
            imageChosenCallback : args.imageChosenCallback,
            input : input,
            setImageURLCallback : setImageURLCallback,
        }));

        return {
            element : element,
            setImageURLCallback : setImageURLCallback,
        };
    },

    /**
     * @param function args.imageChosenCallback
     * @param Element args.input
     * @param function args.setImageURLCallback
     *
     * @return undefined
     */
    handleImageFileChosen : function (args) {
        if (typeof args.imageChosenCallback === "function") {
            args.imageChosenCallback.call(undefined, {
                file : args.input.files[0],
                setImageURLCallback : args.setImageURLCallback,
            });
        }

        args.input.value = null;
    },

    /**
     * @param function args.imageRemovedCallback
     * @param function args.setImageURLCallback
     * @param Event event
     *
     * @return undefined
     */
    handleRemoveElementClicked : function (args, event) {
        args.imageRemovedCallback.call(undefined, {
            setImageURLCallback : args.setImageURLCallback,
        });

        event.stopPropagation();
    },

    /**
     * @param Element args.buttonElement
     * @param Element args.imageElement
     * @param string URL
     *
     * @return undefined
     */
    setImageURL : function (args, URL) {
        if (URL) {
            args.buttonElement.classList.add("image");
            args.imageElement.src = URL;
        } else {
            args.buttonElement.classList.remove("image");
            args.imageElement.src = null;
        }
    },
};
