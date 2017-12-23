"use strict";
/* jshint strict: global */
/* exported CBDefaultEditor */
/* global
    CBUI,
    CBUIExpander */

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
        var item = CBUI.createSectionItem3();

        item.appendPart(CBUI.createMessageSectionItemPart({
            message: "There is no editor available for " +
                     args.spec.className +
                     " models.",
        }));

        section.appendChild(item.element);
        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUIExpander.create({
            message: "Current Spec\n\n--- pre\n" +
                     JSON.stringify(args.spec, null, 2) +
                     "\n---",
        }).element);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
};
