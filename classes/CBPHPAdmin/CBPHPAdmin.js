/* global
    CBPHPAdmin_iniValues,
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby
*/

(function ()
{
    "use strict";

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
            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = key;
            stringsPart.string2 = CBPHPAdmin_iniValues[key] || Colby.nonBreakingSpace;
            stringsPart.element.classList.add("keyvalue");
            stringsPart.element.classList.add("selectable");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
        });

        mainElement.appendChild(sectionElement);
        mainElement.appendChild(CBUI.createHalfSpace());
    },
};

Colby.afterDOMContentLoaded(CBPHPAdmin.init);

}
)() ;
