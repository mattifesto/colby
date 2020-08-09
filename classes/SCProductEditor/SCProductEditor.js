"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* globals
    CBAjax,
    CBErrorHandler,
    CBUI,
    CBUIPanel,
    CBUISpecEditor,
    CBUISpecSaver,
*/


(function () {

    window.SCProductEditor = {
        createImageCollectionEditorElement,
    };



    /**
     * @param CBID productCBID
     *
     * @return Element
     */
    function createImageCollectionEditorElement(
        productCBID
    ) {
        let containerElement = CBUI.createElement(
            "SCProductEditor_artworkCollectonEditorContainer"
        );

        {
            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent = "Product Artwork Collection";

            containerElement.appendChild(
                titleElement
            );
        }

        fetchArtworkCollectionAndDisplayEditor(
            productCBID,
            containerElement
        ).catch(
            function (error) {
                CBErrorHandler.report(
                    error
                );
            }
        );

        return containerElement;
    }
    /* createImageCollectionEditorElement() */



    /**
     * @param CBID productCBID
     *
     * @return Promise -> undefined
     */
    async function fetchArtworkCollectionAndDisplayEditor(
        productCBID,
        artworkCollectionEditorContainerElement
    ) {
        let ajaxResponse = await CBAjax.call(
            "SCProductEditor",
            "fetchArtworkCollectionSpec",
            {
                productCBID,
            }
        );

        let artworkCollectionSpec = ajaxResponse.artworkCollectionSpec;

        let specSaver = CBUISpecSaver.create(
            {
                rejectedCallback: function (error) {
                    CBUIPanel.displayAndReportError(error);
                },
                spec: artworkCollectionSpec,
            }
        );

        /**
         * TODO: we are creating a spec editor inside another spec editor
         * the APIs aren't optimized for this
         * CBModelEditor has code that is like what we need
         * but this should probably have an api to do this exactly
         * this won't save right now
         */
        let artworkCollectionEditor = CBUISpecEditor.create(
            {
                spec: artworkCollectionSpec,
                specChangedCallback: specSaver.specChangedCallback,
                useStrict: true,
            }
        );

        artworkCollectionEditorContainerElement.appendChild(
            artworkCollectionEditor.element
        );
    }
    /* fetchImageCollectionAndDisplayEditor() */

})();
