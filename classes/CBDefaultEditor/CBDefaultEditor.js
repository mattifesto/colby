"use strict";
/* jshint strict: global */
/* exported CBDefaultEditor */
/* global
    CBUI,
    CBUISectionItem4 */

var CBDefaultEditor = {

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        var element = document.createElement("div");
        element.className = "CBDefaultEditor";

        element.appendChild(CBUI.createHalfSpace());

        var section = CBUI.createSection();
        var item = CBUISectionItem4.create();

        item.appendPart(CBUI.createMessageSectionItemPart({
            message: "There is no editor available for " +
                     args.spec.className +
                     " models.",
        }));

        section.appendChild(item.element);
        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
};
