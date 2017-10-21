"use strict";
/* jshint strict: global */
/* exported CBUISpec */
/* globals
    Colby */

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
    specToDescription: function (spec) {
        if (spec === undefined) { return undefined; }

        var editor = window[spec.className + "Editor"];

        if (editor !== undefined && typeof editor.CBUISpec_toDescription === "function") {
            return editor.CBUISpec_toDescription.call(undefined, spec);
        } else if (editor !== undefined && typeof editor.specToDescription === "function") {
            /* deprecated */
            return editor.specToDescription.call(undefined, spec);
        } else {
            return spec.title;
        }
    },

    /**
     * @param object? spec
     *
     * @return string|undefined
     */
    specToThumbnailURI: function (spec) {
        if (spec === undefined) { return undefined; }

        var editor = window[spec.className + "Editor"];

        if (editor !== undefined && typeof editor.CBUISpec_toThumbnailURI === "function") {
            return editor.CBUISpec_toThumbnailURI.call(undefined, spec);
        } else if (editor !== undefined && typeof editor.specToThumbnailURI === "function") {
            /* deprecated */
            return editor.specToThumbnailURI.call(undefined, spec);
        } else if (spec.image) {
            return Colby.imageToURL(spec.image, 'rw320');
        } else {
            return undefined;
        }
    },
};
