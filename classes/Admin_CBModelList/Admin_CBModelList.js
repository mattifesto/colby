"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBImage,
    CBUI,
    CBUIThumbnailPart,
    Colby,

    CBModelsAdmin_classHasTemplates,
    CBModelsAdmin_modelClassName,
    CBModelsAdmin_modelList,
*/

(function () {

    let mainElement;

    Colby.afterDOMContentLoaded(
        function () {
            mainElement = document.getElementsByTagName("main")[0];

            if (
                mainElement === undefined ||
                !mainElement.classList.contains("Admin_CBModelList")
            ) {
                mainElement = undefined;
                return;
            }

            renderModelList();
        }
    );



    /* -- closures -- -- -- -- -- */



    /**
     * @param object modelListItem
     *
     * @return Element
     */
    function createModelListItemElement(
        modelListItem
    ) {
        let elements = CBUI.createElementTree(
            [
                "CBUI_sectionItem",
                "a"
            ],
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "title CBUI_ellipsis"
        );

        let sectionItemElement = elements[0];

        sectionItemElement.href = (
            "/admin/?c=CBModelEditor&ID=" +
            encodeURIComponent(modelListItem.ID)
        );

        /* thumbnail part */

        let thumbnailPart = CBUIThumbnailPart.create();

        sectionItemElement.insertBefore(
            thumbnailPart.element,
            sectionItemElement.firstElementChild
        );

        if (modelListItem.image) {
            thumbnailPart.src = CBImage.toURL(
                modelListItem.image,
                'rs200clc200'
            );
        }


        /* title */

        let textContainerElement = elements[1];
        let titleElement = elements[2];

        let title = (
            modelListItem.title ?
            modelListItem.title.trim() :
            ''
        );

        if (title === "") {
            title = CBModelsAdmin_modelClassName + " (no title)";
        }

        titleElement.textContent = title;


        /* description */

        let descriptionElement = CBUI.createElement(
            "description CBUI_textColor2 CBUI_textSize_small CBUI_ellipsis"
        );

        textContainerElement.appendChild(
            descriptionElement
        );

        descriptionElement.textContent = modelListItem.ID;


        /* navigation arrow */

        sectionItemElement.appendChild(
            CBUI.createElement(
                "CBUI_navigationArrow"
            )
        );

        return sectionItemElement;
    }
    /* createModelSectionItemElement() */



    /**
     * @return undefined
     */
    function renderModelList() {
        let titleItem = CBUI.createHeaderItem();
        titleItem.textContent = CBModelsAdmin_modelClassName + " Models";

        let rightElements;

        if (CBModelsAdmin_classHasTemplates) {
            var createItem = CBUI.createHeaderItem();
            createItem.textContent = "Create";
            createItem.href = (
                "/admin/?c=CBModelsAdminTemplateSelector&modelClassName=" +
                CBModelsAdmin_modelClassName
            );

            rightElements = [createItem.element];
        }

        mainElement.appendChild(
            CBUI.createHeader(
                {
                    centerElement: titleItem.element,
                    rightElements: rightElements,
                }
            )
        );

        if (CBModelsAdmin_modelList.length > 0) {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            CBModelsAdmin_modelList.forEach(
                function (modelListItem) {
                    sectionElement.appendChild(
                        createModelListItemElement(modelListItem)
                    );
                }
            );

            mainElement.appendChild(sectionContainerElement);
        }
    }
    /* renderModelList() */

})();
