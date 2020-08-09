"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBArtwork,
    CBArtworkElement,
    CBUI,
    Colby,
*/


(function () {

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName(
            "CBArtworkCollectionView"
        );

        for (let index = 0; index < elements.length; index += 1) {
            let element = elements.item(index);

            initializeElement(element);
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function createThumbnailsElement(
        artworks,
        thumbnailClickedCallback
    ) {
        let elements = CBUI.createElementTree(
            "CBArtworkCollectionView_thumbnails"
        );

        let element = elements[0];

        for (
            let artworkIndex = 0;
            artworkIndex < artworks.length;
            artworkIndex += 1
        ) {
            const currentArtworkIndex = artworkIndex;
            const currentThumbnailClickedCallback = thumbnailClickedCallback;
            let artwork = artworks[artworkIndex];

            let thumbnailImageURL = CBArtwork.getThumbnailImageURL(
                artwork
            );

            if (thumbnailImageURL === "") {
                continue;
            }

            let elements = CBUI.createElementTree(
                "CBArtworkCollectionView_thumbnail"
            );

            let thumbnailElement = elements[0];

            element.appendChild(
                thumbnailElement
            );

            thumbnailElement.addEventListener(
                "click",
                function () {
                    currentThumbnailClickedCallback(
                        currentArtworkIndex
                    );
                }
            );

            /* image */

            let artworkElement = CBArtworkElement.create(
                {
                    URL: thumbnailImageURL,
                }
            );

            thumbnailElement.appendChild(
                artworkElement
            );
        }

        return element;
    }
    /* createThumbnailsElement() */



    /**
     * @param Element element
     *
     * @return undefined
     */
    function initializeElement(
        element
    ) {
        let artworks = JSON.parse(
            element.dataset.artworks
        );

        let imageContainerElement = CBUI.createElement(
            "CBArtworkCollectionView_imageContainer"
        );

        element.appendChild(
            imageContainerElement
        );

        element.appendChild(
            createThumbnailsElement(
                artworks,
                function (index) {
                    showArtworkAtIndex(index);
                }
            )
        );

        showArtworkAtIndex(0);


        /* -- closures -- */



        /**
         *
         */
        function showArtworkAtIndex(
            index
        ) {
            let artwork = artworks[index];

            let mediumImageURL = CBArtwork.getMediumImageURL(
                artwork
            );

            let artworkElement = CBArtworkElement.create(
                {
                    URL: mediumImageURL,
                }
            );

            imageContainerElement.textContent = "";

            imageContainerElement.appendChild(
                artworkElement
            );
        }
        /* showArtworkAtIndex() */

    }
    /* initializeElement() */

})();
