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
     * @param object spec
     *
     * @return string|undefined
     *
     *      The implementation of CBUISpec_toDescription() should return
     *      undefined if the spec has no description to offer. This will signal
     *      to the caller that it can try other options for finding a
     *      description.
     */
    specToDescription: function (spec) {
        spec = CBConvert.valueAsModel(spec);

        if (spec === undefined) {
            return undefined;
        }

        let editorObject = window[spec.className + "Editor"];
        let callable = CBModel.valueAsFunction(
            editorObject,
            "CBUISpec_toDescription"
        );

        if (callable !== undefined) {
            return callable(spec);
        } else {
            return CBModel.valueToString(
                spec,
                "title"
            ).trim() || undefined;
        }
    },
    /* specToDescription() */



    /**
     * @param object spec
     *
     * @return string|undefined
     *
     *      The implementation of CBUISpec_toThumbnailURL() should return
     *      undefined if the spec has no thumbnail URL to offer. This will
     *      signal to the caller that it can try other options for finding a
     *      thumbnail URL.
     */
    specToThumbnailURL: function (spec) {
        spec = CBConvert.valueAsModel(spec);

        if (spec === undefined) {
            return undefined;
        }

        let editorObject = window[spec.className + "Editor"];
        let callable = CBModel.valueAsFunction(
            editorObject,
            "CBUISpec_toThumbnailURL"
        );

        if (callable !== undefined) {
            return callable(spec);
        } else if (spec.image) {
            return CBImage.toURL(
                spec.image,
                'rw320'
            );
        } else {
            return undefined;
        }
    },
    /* specToThumbnailURL() */

};
/* CBUISpec */
