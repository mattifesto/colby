/* globals
    CBArtwork,
    CBImage,
*/


(function () {
    "use strict";

    window.CBArtworkCollectionView = {
        create: CBArtworkCollectionView_create,
    };

    {
        let elements = document.getElementsByClassName(
            "CBArtworkCollectionView_root_element"
        );

        for (
            let index = 0;
            index < elements.length;
            index += 1
        ) {
            let element = elements.item(
                index
            );

            initializeElement(
                element
            );
        }
    }



    /* -- functions -- */



    /**
     * @return Element
     */
    function
    CBArtworkCollectionView_create(
    ) /* -> Element */
    {
        let rootElement = document.createElement(
            "div"
        );

        rootElement.className = "CBArtworkCollectionView_root_element";

        return rootElement;
    }
    /* CBArtworkCollectionView_create() */



    /**
     * @return Element
     */
    function
    createThumbnailsElement(
        artworks,
        thumbnailClickedCallback
    ) /* -> Element */
    {
        let thumbnailsContainerElement = document.createElement(
            "div"
        );

        thumbnailsContainerElement.className =
        "CBArtworkCollectionView_thumbnailsContainer_element";

        for (
            let artworkIndex = 0;
            artworkIndex < artworks.length;
            artworkIndex += 1
        ) {
            const currentArtworkIndex = artworkIndex;
            const currentThumbnailClickedCallback = thumbnailClickedCallback;



            let thumbnailPictureElement;
            let artworkModel = artworks[artworkIndex];

            let imageModel = CBArtwork.getImage(
                artworkModel
            );

            if (
                imageModel !== undefined
            ) {
                thumbnailPictureElement = CBImage.createPictureElement(
                    imageModel,
                    'rw1280'
                );
            } else {
                thumbnailPictureElement = CBImage.createPictureElement(
                    CBArtwork.getThumbnailImageURL(
                        artworkModel
                    )
                );
            }

            thumbnailPictureElement.className = (
                "CBArtworkCollectionView_thumbnailPicture_element"
            );

            thumbnailPictureElement.addEventListener(
                "click",
                function () {
                    currentThumbnailClickedCallback(
                        currentArtworkIndex
                    );
                }
            );

            thumbnailsContainerElement.appendChild(
                thumbnailPictureElement
            );
        }

        return thumbnailsContainerElement;
    }
    /* createThumbnailsElement() */



    /**
     * @param Element element
     *
     * @return undefined
     */
    function
    initializeElement(
        element
    ) /* -> undefined */
    {
        let artworks = JSON.parse(
            element.dataset.artworks
        );

        let contentElement = element.getElementsByClassName(
            "CBArtworkCollectionView_content_element"
        )[0];

        let mainPictureContainerElement =
        element.getElementsByClassName(
            "CBArtworkCollectionView_mainPictureContainer_element"
        )[0];

        {
            let thumbnailsContainerElement = createThumbnailsElement(
                artworks,
                function (
                    index
                ) {
                    showArtworkAtIndex(
                        index
                    );
                }
            );

            contentElement.appendChild(
                thumbnailsContainerElement
            );
        }

        showArtworkAtIndex(0);



        /**
         * @param int index
         *
         * @return undefined
         */
        function
        showArtworkAtIndex(
            index
        ) /* -> undefined */
        {
            let mainPictureElement;
            let artworkModel = artworks[index];

            let imageModel = CBArtwork.getImage(
                artworkModel
            );

            if (
                imageModel !== undefined
            ) {
                mainPictureElement = CBImage.createPictureElement(
                    imageModel,
                    'rw1280'
                );
            } else {
                mainPictureElement = CBImage.createPictureElement(
                    CBArtwork.getMediumImageURL(
                        artworkModel
                    )
                );
            }

            mainPictureElement.className = (
                "CBArtworkCollectionView_mainPicture_element"
            );

            mainPictureContainerElement.textContent = "";

            mainPictureContainerElement.append(
                mainPictureElement
            );
        }
        /* showArtworkAtIndex() */

    }
    /* initializeElement() */

})();
