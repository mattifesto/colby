/* globals
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
*/

(function () {
    "use strict";

    window.CBUIBooleanEditor2 = {
        createObjectPropertyEditorElement
    };



    /**
     * @see documentation
     *
     * @param object targetObject
     * @param string targetPropertyName
     * @param string title
     * @param function changedEventListener
     *
     * @return Element
     */
    function
    createObjectPropertyEditorElement(
        targetObject,
        targetPropertyName,
        title,
        changedEventListener
    ) {
        let elements = CBUI.createElementTree(
            "CBUIBooleanEditor2 CBUI_sectionItem",
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "CBUIBooleanEditor2_title"
        );

        let element = elements[0];

        {
            let titleElement = elements[2];

            titleElement.textContent = title;
        }

        let switchPart = CBUIBooleanSwitchPart.create();

        switchPart.value = CBModel.valueToBool(
            targetObject,
            targetPropertyName
        );

        element.appendChild(
            switchPart.element
        );

        switchPart.changed = function () {
            targetObject[targetPropertyName] = switchPart.value;

            changedEventListener();
        };

        return element;
    }
    /* createObjectPropertyEditorElement() */

})();
