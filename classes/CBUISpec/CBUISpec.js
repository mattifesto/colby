"use strict";

var CBUISpec = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return mixed
     */
    setValue : function (args, value) {
        args.spec[args.propertyName] = value;
        args.specChangedCallback.call();
    },

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
