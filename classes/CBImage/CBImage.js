/* global
    CBConvert,
    CBDataStore,
    CBException,
    CBModel,
*/


(function () {
    "use strict";

    window.CBImage =
    {
        /* accessors */

        getExtension:
        CBImage_getExtension,

        getHeight:
        CBImage_getHeight,

        getWidth:
        CBImage_getWidth,

        /* functions */

        createPictureElement:
        CBImage_createPictureElement,

        createPictureElementWithImageSize:
        CBImage_createPictureElementWithImageSize,

        toURL:
        CBImage_toURL,
    };



    /* -- accessors -- */



    /**
     * @param object imageModel
     *
     * @return string
     */
    function
    CBImage_getExtension(
        imageModel
    ) {
        return CBModel.valueToString(
            imageModel,
            "extension"
        );
    }
    /* CBImage_getExtension() */



    /**
     * @param object imageModel
     *
     * @return int|undefined
     */
    function
    CBImage_getHeight(
        imageModel
    ) {
        return CBModel.valueAsInt(
            imageModel,
            "height"
        );
    }
    /* CBImage_getHeight() */



    /**
     * @param object imageModel
     *
     * @return int|undefined
     */
    function
    CBImage_getWidth(
        imageModel
    ) {
        return CBModel.valueAsInt(
            imageModel,
            "width"
        );
    }
    /* CBImage_getWidth() */



    /* -- functions -- */



    /**
     * @param object|string imageModelOrURL
     *
     *      Either a CBImage model or a URL.
     *
     * @param string|undefined imageResizeOperation
     *
     *      This must be specified if a CBImage model was provided.
     *
     * @return Element
     *
     *      {
     *          CBImage_getImgElement() -> Element
     *      }
     */
    function
    CBImage_createPictureElement(
        /* object|string */ imageModelOrURL,
        /* string|undefined */ imageResizeOperation
    ) /* -> Element */
    {
        let pictureElement = document.createElement(
            "picture"
        );

        let imgElement = document.createElement(
            "img"
        );

        if (
            typeof imageModelOrURL === "object"
        ) {
            let imageModel = imageModelOrURL;

            if (
                typeof imageResizeOperation !== "string"
            ) {
                throw CBException.withValueRelatedError(
                    Error(
                        CBConvert.stringToCleanLine(`

                            The imageResizeOperation argument is not valid.

                        `)
                    ),
                    imageResizeOperation,
                    "21c7d3c5e8249cd02586d8a31b84a0bcd067eb3f"
                );
            }

            let imageExtension = CBImage_getExtension(
                imageModel
            );

            if (
                imageExtension !== "webp"
            ) {
                let sourceElement = document.createElement(
                    "source"
                );

                sourceElement.srcset = CBImage_toURL(
                    imageModel,
                    imageResizeOperation,
                    "webp"
                );

                sourceElement.type = "image/webp";

                pictureElement.append(
                    sourceElement
                );
            }

            imgElement.src = CBImage_toURL(
                imageModel,
                imageResizeOperation
            );
        } else {
            let imageURL = imageModelOrURL;

            imgElement.src = imageURL;
        }

        pictureElement.append(
            imgElement
        );

        pictureElement.CBImage_getImgElement = function () {
            return imgElement;
        };

        return pictureElement;
    }
    /* CBImage_createPictureElement() */



    /**
     * @param object|string imageModelOrURL
     *
     *      Either a CBImage model or a URL.
     *
     * @param string|undefined imageResizeOperation
     *
     *      This must be specified if a CBImage model was provided.
     *
     * @param int imageWidth
     * @param int imageHeight
     *
     * @return Element
     *
     *      {
     *          CBImage_getImgElement() -> Element
     *      }
     */
    function
    CBImage_createPictureElementWithImageSize(
        /* object|string */ imageModelOrURL,
        /* string|undefined */ imageResizeOperation,
        /* int */ imageWidth,
        /* int */ imageHeight
    ) // -> Element
    {
        let pictureElement = CBImage_createPictureElement(
            imageModelOrURL,
            imageResizeOperation
        );

        let imgElement = pictureElement.CBImage_getImgElement();

        imgElement.width = imageWidth;
        imgElement.height = imageHeight;

        return pictureElement;
    }
    /* CBImage_createPictureElementWithImageSize() */



    /**
     * @param object imageModel
     *
     *      A CBImage model.
     *
     * @param string? imageResizeOperation
     *
     *      If specified, this value will be used instead of the filename
     *      property value in the image model. This allows the caller to specify
     *      a smaller size, such as "rw320", rather than "original", the most
     *      common filename property value of an image model.
     *
     * @return string
     *
     *      Returns an empty string if there is an issue with the parameters.
     */
    function
    CBImage_toURL(
        imageModel,
        imageResizeOperation,
        imageExtension
    ) {
        let filename;

        let imageModelCBID = CBModel.getCBID(
            imageModel,
        );

        if (
            imageModelCBID === undefined
        ) {
            return "";
        }

        if (
            typeof imageResizeOperation === "string"
        ) {
            filename = imageResizeOperation;
        }

        else {
            filename = CBModel.valueToString(
                imageModel,
                "filename"
            );
        }

        if (
            filename === ""
        ) {
            return "";
        }

        if (
            typeof imageExtension !== "string"
        ) {
            imageExtension = CBImage_getExtension(
                imageModel
            );
        }

        if (
            imageExtension === ""
        ) {
            return "";
        }

        return (
            "/" +
            CBDataStore.flexpath(
                imageModelCBID,
                (
                    filename +
                    "." +
                    imageExtension
                )
            )
        );
    }
    /* CBImage_toURL() */

})();
