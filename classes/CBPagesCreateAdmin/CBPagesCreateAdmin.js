"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPagesCreateAdmin */
/* global
    CBPagesCreateAdmin_templates,
    CBUI,
    CBUINavigationArrowPart,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby */

var CBPagesCreateAdmin = {

    init: function () {
        let main = document.getElementsByTagName("main")[0];

        main.appendChild(CBUI.createHalfSpace());

        let sectionElement = CBUI.createSection();

        CBPagesCreateAdmin_templates.forEach(function (template) {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                let ID = Colby.random160();
                let URI = `/admin/?c=CBModelEditor&ID=${ID}&templateClassName=${template.className}`;

                window.location = URI;
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = template.title;

            sectionItem.appendPart(stringsPart);
            sectionItem.appendPart(CBUINavigationArrowPart.create());
            sectionElement.appendChild(sectionItem.element);
        });

        main.appendChild(sectionElement);
        main.appendChild(CBUI.createHalfSpace());
    },
};

Colby.afterDOMContentLoaded(CBPagesCreateAdmin.init);
