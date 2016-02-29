"use strict";

var CBUISpec = {

    /**
     * @param object? spec
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        if (spec === undefined) { return undefined; }

        var editor = window[spec.className + "Editor"];

        if (editor !== undefined && typeof editor.specToDescription === "function") {
            return editor.specToDescription.call(undefined, spec);
        } else {
            return spec.title;
        }
    },
};
