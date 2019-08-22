"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBNoteView */
/* global
    CBModel,
    CBUI,
    Colby,
*/

var CBNoteView = {

    /**
     * @param object model
     *
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *      }
     */
    CBView_create: function (model) {
        let element = CBUI.createElement("CBNoteView");

        let headerElement = CBUI.createElement("CBUI_content");

        element.appendChild(headerElement);

        let timeElement = Colby.unixTimestampToElement(
            CBModel.valueAsInt(model, "note.timestamp"),
            "",
            "CBUI_textColor2 compact"
        );

        headerElement.appendChild(timeElement);

        let textElement = CBUI.createElement("CBNoteView_text");

        element.appendChild(textElement);

        textElement.textContent = CBModel.valueToString(model, "note.text");

        return {
            get element() {
                return element;
            }
        };
    },
    /* CBView_create() */
};
