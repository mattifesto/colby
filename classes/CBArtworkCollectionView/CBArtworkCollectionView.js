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
     * @param Element thumbnailsContainerElementArgument
     * @param [object] artworks
     * @param function thumbnailClickedCallback
     *
     * @return undefined
     */
    function
    CBArtworkCollectionView_initThumbnails(
        thumbnailsContainerElementArgument,
        artworks,
        thumbnailClickedCallback
    ) // -> undefined
    {
        for (
            let artworkIndex = 0;
            artworkIndex < artworks.length;
            artworkIndex += 1
        ) {
            const currentArtworkIndex = artworkIndex;
            const currentThumbnailClickedCallback = thumbnailClickedCallback;

            let thumbnailPictureElement =
            thumbnailsContainerElementArgument.children[
                artworkIndex
            ];

            thumbnailPictureElement.addEventListener(
                "click",
                function () {
                    currentThumbnailClickedCallback(
                        currentArtworkIndex
                    );
                }
            );
        }
        // for
    }
    // CBArtworkCollectionView_initThumbnails()



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

        let mainPictureContainerElement =
        element.getElementsByClassName(
            "CBArtworkCollectionView_mainPictureContainer_element"
        )[0];

        {
            let thumbnailsContainerElement =
            element.getElementsByClassName(
                "CBArtworkCollectionView_thumbnailsContainer_element"
            )[0];

            CBArtworkCollectionView_initThumbnails(
                thumbnailsContainerElement,
                artworks,
                function (
                    index
                ) {
                    showArtworkAtIndex(
                        index
                    );
                }
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
