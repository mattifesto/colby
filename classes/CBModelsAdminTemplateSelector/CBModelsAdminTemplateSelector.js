"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelsAdminTemplateSelector */
/* global
    CBModelsAdminTemplateSelector_modelClassName,
    CBModelsAdminTemplateSelector_templates,
    CBUI,
    CBUIMessagePart,
    CBUINavigationArrowPart,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby */

var CBModelsAdminTemplateSelector = {

    /**
     * @return undefined
     */
    init: function () {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(CBUI.createHalfSpace());

        let sectionElement = CBUI.createSection();

        if (CBModelsAdminTemplateSelector_templates.length > 0) {
            CBModelsAdminTemplateSelector_templates.forEach(function (template) {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    let ID = Colby.random160();
                    let URL = `/admin/?c=CBModelEditor&ID=${ID}&templateClassName=${template.className}`;

                    window.location = URL;
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = template.title;

                sectionItem.appendPart(stringsPart);
                sectionItem.appendPart(CBUINavigationArrowPart.create());
                sectionElement.appendChild(sectionItem.element);
            });
        } else {
            let sectionItem = CBUISectionItem4.create();
            let messagePart = CBUIMessagePart.create();
            messagePart.message = `

                --- center
                There are no model templates available for
                ${CBModelsAdminTemplateSelector_modelClassName} models.
                ---

            `;

            sectionItem.appendPart(messagePart);
            sectionElement.appendChild(sectionItem.element);
        }

        mainElement.appendChild(sectionElement);
        mainElement.appendChild(CBUI.createHalfSpace());
    },
};

Colby.afterDOMContentLoaded(CBModelsAdminTemplateSelector.init);
