"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* globals
    CBAjax,
    CBErrorHandler,
    CBModel,
    CBSpecSaver,
    CBUI,
    CBUIPanel,
    CBUISpecEditor,
*/


(function () {

    window.SCProductEditor = {
        CBUISpecEditor_createEditorElement: createEditorElement,
        createImageCollectionEditorElement,
    };



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    function createEditorElement(
        args
    ) {
        let spec = args.spec;

        let elements = CBUI.createElementTree(
            "SCProductEditor",
            "CBUI_sectionContainer",
            "CBUI_section",
        );

        let element = elements[0];
        let sectionElement = elements[2];


        /* product code */

        sectionElement.appendChild(
            createProductCodeElement(
                spec
            )
        );


        /* product title */

        sectionElement.appendChild(
            createProductTitleElement(
                spec
            )
        );




        /* product images */

        element.appendChild(
            createImageCollectionEditorElement(
                spec.ID
            )
        );

        return element;
    }
    /* createEditorElement() */



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
     * @param object productSpec
     *
     * @return Element
     */
    function createProductCodeElement(
        productSpec
    ) {
        let elements = CBUI.createElementTree(
            "SCProductEditor_productCodeContainer CBUI_container_topAndBottom",
            "SCProductEditor_productCodeLabel CBUI_textColor2"
        );

        let element = elements[0];

        elements[1].textContent = "Product Code";

        let productCode = CBModel.valueToString(
            productSpec,
            'productCode'
        );

        let productCodeValueElement = CBUI.createElement(
            "SCProductEditor_productCodeValue"
        );

        element.appendChild(
            productCodeValueElement
        );

        productCodeValueElement.textContent = productCode;

        return element;
    }
    /* createProductCodeElement() */



    /**
     * @param object productSpec
     *
     * @return Element
     */
    function createProductTitleElement(
        productSpec
    ) {
        let elements = CBUI.createElementTree(
            "SCProductEditor_productTitleContainer CBUI_container_topAndBottom",
            "SCProductEditor_productTitleLabel CBUI_textColor2"
        );

        let element = elements[0];

        elements[1].textContent = "Product Title";

        let title = CBModel.valueToString(
            productSpec,
            'title'
        );

        let productTitleValueElement = CBUI.createElement(
            "SCProductEditor_productTitleValue"
        );

        element.appendChild(
            productTitleValueElement
        );

        productTitleValueElement.textContent = title;

        return element;
    }
    /* createProductTitleElement() */



    /**
     * @param CBID productCBID
     *
     * @return Promise -> undefined
     */
    async function fetchArtworkCollectionAndDisplayEditor(
        productCBID,
        artworkCollectionEditorContainerElement
    ) {
        let mostRecentSavePromise;

        let ajaxResponse = await CBAjax.call(
            "SCProductEditor",
            "fetchArtworkCollectionSpec",
            {
                productCBID,
            }
        );

        let artworkCollectionSpec = ajaxResponse.artworkCollectionSpec;

        let specSaver2 = CBSpecSaver.create(
            artworkCollectionSpec
        );

        let artworkCollectionEditor = CBUISpecEditor.create(
            {
                spec: artworkCollectionSpec,

                useStrict: true,

                specChangedCallback: function () {
                    let savePromise = specSaver2.CBSpecSaver_save();

                    if (savePromise !== mostRecentSavePromise) {
                        mostRecentSavePromise = savePromise;

                        savePromise.catch(
                            function (error) {
                                CBUIPanel.displayAndReportError(
                                    error
                                );
                            }
                        ).finally(
                            function () {
                                if (savePromise === mostRecentSavePromise) {
                                    mostRecentSavePromise = undefined;
                                }
                            }
                        );
                    }
                },
            }
        );

        artworkCollectionEditorContainerElement.appendChild(
            artworkCollectionEditor.element
        );
    }
    /* fetchImageCollectionAndDisplayEditor() */

})();
