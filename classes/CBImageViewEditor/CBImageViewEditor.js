"use strict";
/* jshint strict: global */
/* exported CBImageViewEditor */
/* global
    CBUI */

var CBImageViewEditor = {

    /**
     * @return Element
     */
    createEditor: function (args) {
         var section, item;
         var element = document.createElement("div");
         element.className = "CBImageViewEditor";

         element.appendChild(CBUI.createHalfSpace());

         section = CBUI.createSection();
         item = CBUI.createSectionItem();
         item.textContent = "This view has been deprecated.";
         item.style.padding = "10px";
         item.style.textAlign = "center";
         section.appendChild(item);
         element.appendChild(section);

         element.appendChild(CBUI.createHalfSpace());

         return element;
    },

    /**
     * @param object spec
     *
     * @return string|undefined
     */
    specToDescription: function (spec) {
        if (spec.alternativeTextViewModel) {
            return spec.alternativeTextViewModel.text;
        } else {
            return undefined;
        }
    },
};
