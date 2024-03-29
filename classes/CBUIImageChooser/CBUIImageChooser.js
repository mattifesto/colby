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
(function () {
    "use strict";

    window.CBUIImageChooser =
    {
        create:
        CBUIImageChooser_create,

        createFullSizedChooser:
        CBUIImageChooser_createFullSizeChooser,

        createThumbnailSizedChooser:
        CBUIImageChooser_createThumbnailSizedChooser,
    };



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
    function
    CBUIImageChooser_create(
    ) // -> object
    {
        var chosen, removed, src;

        var element = document.createElement("div");
        element.className = "CBUIImageChooser";

        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.style.display = "none";
        inputElement.accept="image/jpeg, image/png, image/gif";

        element.appendChild(inputElement);

        var imageContainerElement = document.createElement("div");
        imageContainerElement.className = "CBUIImageChooser_container";
        var imageElement = document.createElement("img");
        imageElement.style.display = "none";

        imageContainerElement.appendChild(imageElement);
        element.appendChild(imageContainerElement);

        var captionElement = document.createElement("div");
        captionElement.className = "CBUIImageChooser_caption";
        captionElement.style.display = "none";

        element.appendChild(captionElement);

        var commandsElement = document.createElement("div");
        commandsElement.className = "CBUIImageChooser_commands";

        var chooseElement = document.createElement("div");
        chooseElement.textContent = "choose";

        chooseElement.addEventListener(
            "click",
            function () {
                inputElement.click();
            }
        );

        inputElement.addEventListener(
            "change",
            function () {
                if (typeof chosen === "function") {

                    /**
                     * @deprecated use imageChooser API
                     */
                    let chosenArgs = {

                        /**
                         * @deprecated use imageChooser.file
                         */
                        get file() {
                            return inputElement.files[0];
                        },

                        /**
                         * @deprecated use imageChooser.caption
                         */
                        setCaptionCallback: function (value) {
                            api.caption = value;
                        },

                        /**
                         * @deprecated use imageChooser.src
                         */
                        setImageURI: function (value) {
                            api.src = value;
                        },

                        /**
                         * @deprecated use imageChooser.src
                         */
                        setImageURLCallback: function (value) {
                            api.src = value;
                        },
                    };

                    chosen(chosenArgs);
                }

                inputElement.value = null;
            }
        );

        commandsElement.appendChild(chooseElement);

        var removeElement = document.createElement("div");
        removeElement.style.display = "none";
        removeElement.textContent = "remove";

        removeElement.addEventListener(
            "click",
            function () {
                api.caption = "";
                api.src = "";

                if (typeof removed === "function") {
                    removed(
                        {
                            setImageURI: function (value) {
                                api.src = value;
                            }, /* deprecated */
                            setImageURLCallback: function (value) {
                                api.src = value;
                            }, /* deprecated */
                        }
                    );
                }
            }
        );

        commandsElement.appendChild(removeElement);
        element.appendChild(commandsElement);

        let api = {
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

            /**
             * @return File|undefined
             */
            get file() {
                return inputElement.files[0];
            },

            get removed() {
                return removed;
            },
            set removed(value) {
                removed = value;
            },
            get src() {
                return src;
            },
            set src(value) {
                src = value;

                /**
                 * @NOTE 2018.03.08
                 *
                 *      There is much discussion on the internet about setting
                 *      the src property of and img element to a falsey value.
                 *      While modern browsers should handle it properly, it
                 *      considered an odd thing to do. The idea is that an img
                 *      element is supposed to have a valid src value and if it
                 *      doesn't why do you even have an img element and why
                 *      would you display it?
                 *
                 *      For a CBUIImageChooser, if the user sets the src to a
                 *      falsey value, that has a meaning: there is currently no
                 *      image chosen. When this happens, the img element src is
                 *      not changed, but the img element is not displayed. If
                 *      the user ever sets the src to a non-falsey value the img
                 *      element's src property will be set an the img element
                 *      will be displayed again.
                 */

                if (src) {
                    imageElement.src = src;
                    imageElement.style.display = "block";
                    removeElement.style.display = "block";
                } else {
                    imageElement.style.display = "none";
                    removeElement.style.display = "none";
                }
            },
        };

        return api;
    }
    // CBUIImageChooser_create()



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
    function
    CBUIImageChooser_createFullSizeChooser(
        args
    ) // -> object
    {
        let chooser =
        CBUIImageChooser_create();

        chooser.chosen = args.imageChosenCallback;
        chooser.removed = args.imageRemovedCallback;

        chooser.setCaptionCallback = function (value) {
            chooser.caption = value;
        }; /* deprecated */

        chooser.setImageURI = function (value) {
            chooser.src = value;
        }; /* deprecated */

        chooser.setImageURLCallback = function (value) {
            chooser.src = value;
        }; /* deprecated */

        return chooser;
    }
    // CBUIImageChooser_createFullSizeChooser()



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
    function
    CBUIImageChooser_createThumbnailSizedChooser(
        args
    ) // -> object
    {
        let chooser =
        CBUIImageChooser_create();
        
        chooser.element.classList.add("CBUIImageChooser_thumbnail");

        chooser.chosen = args.imageChosenCallback;
        chooser.removed = args.imageRemovedCallback;

        chooser.setCaptionCallback = function (value) {
            chooser.caption = value;
        }; /* deprecated */

        chooser.setImageURI = function (value) {
            chooser.src = value;
        }; /* deprecated */

        chooser.setImageURLCallback = function (value) {
            chooser.src = value;
        }; /* deprecated */

        return chooser;
    }
    // CBUIImageChooser_createThumbnailSizedChooser()

})();
