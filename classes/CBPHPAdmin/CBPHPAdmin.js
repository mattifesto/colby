"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPHPAdmin */
/* global
    CBPHPAdmin_iniValues,
    CBUI,
    CBUISectionItem4,
    CBUITitleAndDescriptionPart,
    Colby */

var CBPHPAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        let mainElement = document.getElementsByTagName("main")[0];
        let sectionElement = CBUI.createSection();

        mainElement.appendChild(CBUI.createHalfSpace());

        Object.keys(CBPHPAdmin_iniValues).forEach(function (key) {
            let sectionItem = CBUISectionItem4.create();
            let titleAndDescriptionPart = CBUITitleAndDescriptionPart.create();
            titleAndDescriptionPart.title = key;
            titleAndDescriptionPart.description = CBPHPAdmin_iniValues[key] || Colby.nonBreakingSpace;

            sectionItem.appendPart(titleAndDescriptionPart);
            sectionElement.appendChild(sectionItem.element);
        });

        mainElement.appendChild(sectionElement);
        mainElement.appendChild(CBUI.createHalfSpace());
    },
};

Colby.afterDOMContentLoaded(CBPHPAdmin.init);
