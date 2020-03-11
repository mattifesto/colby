"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelsAdminTemplateSelector */
/* global
    CBUI,
    Colby,

    CBModelsAdminTemplateSelector_modelClassName,
    CBModelsAdminTemplateSelector_templates,
*/


(function () {

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let element;
        let sectionElement;

        {
            let elements = document.getElementsByClassName(
                "CBModelsAdminTemplateSelector"
            );

            if (elements.length < 1) {
                return;
            }

            element = elements[0];

            elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            sectionElement = elements[1];
        }

        if (CBModelsAdminTemplateSelector_templates.length > 0) {
            CBModelsAdminTemplateSelector_templates.forEach(
                function (template) {
                    let sectionItemElement;

                    {
                        let elements = CBUI.createElementTree(
                            "CBUI_sectionItem",
                            "CBUI_container_topAndBottom CBUI_flexGrow",
                            "title"
                        );

                        sectionElement.appendChild(
                            elements[0]
                        );

                        sectionItemElement = elements[0];
                        let titleElement = elements[2];

                        titleElement.textContent = template.title;

                        sectionItemElement.appendChild(
                            CBUI.createElement(
                                "CBUI_navigationArrow"
                            )
                        );
                    }

                    sectionItemElement.addEventListener(
                        "click",
                        function () {
                            let CBID = Colby.random160();

                            let URL = (
                                `/admin/` +
                                `?c=CBModelEditor` +
                                `&ID=${CBID}` +
                                `&templateClassName=${template.className}`
                            );

                            window.location.href = URL;
                        }
                    );
                }
            );
        } else {
            sectionElement.appendChild(
                CBUI.cbmessageToElement(`

                    --- center
                    There are no model templates available for
                    ${CBModelsAdminTemplateSelector_modelClassName} models.
                    ---

                `)
            );
        }
    }
    /* afterDOMContentLoaded() */

})();
