"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpec */
/* globals
    CBConvert,
    CBImage,
    CBModel,
*/

var CBUISpec = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return mixed
     */
    setValue: function (args, value) {
        args.spec[args.propertyName] = value;
        args.specChangedCallback.call();
    },
    /* setValue() */


    /**
     * @param object? spec
     *
     * @return string|undefined
     */
    specToDescription: function (spec) {
        spec = CBConvert.valueAsModel(spec);

        if (spec === undefined) {
            return undefined;
        }

        var editor = window[spec.className + "Editor"];

        if (
            editor !== undefined &&
            typeof editor.CBUISpec_toDescription === "function"
        ) {
            return editor.CBUISpec_toDescription.call(undefined, spec);
        }

        /* deprecated */
        else if (
            editor !== undefined &&
            typeof editor.specToDescription === "function"
        ) {
            return editor.specToDescription.call(undefined, spec);
        }

        else {
            return CBModel.valueToString(spec, "title");
        }
    },
    /* specToDescription() */


    /**
     * @param object? spec
     *
     * @return string|undefined
     */
    specToThumbnailURI: function (spec) {
        spec = CBConvert.valueAsModel(spec);

        if (spec === undefined) {
            return undefined;
        }

        var editor = window[spec.className + "Editor"];

        if (
            editor !== undefined &&
            typeof editor.CBUISpec_toThumbnailURI === "function"
        ) {
            return editor.CBUISpec_toThumbnailURI.call(
                undefined,
                spec
            );
        }

        /* deprecated */
        else if (
            editor !== undefined &&
            typeof editor.specToThumbnailURI === "function"
        ) {
            return editor.specToThumbnailURI.call(undefined, spec);
        }

        else if (spec.image) {
            return CBImage.toURL(
                spec.image,
                'rw320'
            );
        }

        else {
            return undefined;
        }
    },
    /* specToThumbnailURI() */
};
/* CBUISpec */
