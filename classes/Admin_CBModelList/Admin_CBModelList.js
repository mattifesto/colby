"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBImage,
    CBUI,
    CBUINavigationArrowPart,
    CBUISectionItem4,
    CBUIThumbnailPart,
    CBUITitleAndDescriptionPart,
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
     * @return Element
     */
    function createModelListItemElement(modelListItem) {
        var sectionItem = CBUISectionItem4.create();

        sectionItem.callback = function () {
            window.location = (
                "/admin/?c=CBModelEditor&ID=" +
                encodeURIComponent(modelListItem.ID)
            );
        };


        /* thumbnail part */

        let thumbnailPart = CBUIThumbnailPart.create();

        sectionItem.appendPart(thumbnailPart);

        if (modelListItem.image) {
            thumbnailPart.src = CBImage.toURL(
                modelListItem.image,
                'rs200clc200'
            );
        }


        /* title and description part */

        var titleAndDescriptionPart =
        CBUITitleAndDescriptionPart.create();

        var title =
        modelListItem.title ?
        modelListItem.title.trim() :
        '';

        if (title === "") {
            title = CBModelsAdmin_modelClassName + " (no title)";
        }

        titleAndDescriptionPart.title = title;
        titleAndDescriptionPart.description = modelListItem.ID;

        sectionItem.appendPart(titleAndDescriptionPart);


        /* navigation arrow */

        sectionItem.appendPart(
            CBUINavigationArrowPart.create()
        );

        return sectionItem.element;
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
