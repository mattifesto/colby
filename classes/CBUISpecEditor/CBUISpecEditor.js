"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpecEditor */
/* global
    CBDefaultEditor,
    CBException,
    CBModel,
*/

var CBUISpecEditor = {

    /**
     * This the core function to call to create an editor for a spec. It has all
     * of the rules built into it and if an editor is being created anywhere
     * else for a spec it should be changed to call this function.
     *
     * @param object args
     *
     *      {
     *          spec: model
     *          specChangedCallback: function
     *          useStrict: bool
     *
     *              If strict is true, an editor will only be created if a
     *              properly implemented editor exists for the spec.
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element|undefined
     *      }
     */
    create: function (args) {
        let useStrict = CBModel.valueToBool(
            args,
            "useStrict"
        );

        let spec = CBModel.valueAsModel(
            args,
            "spec"
        );

        if (spec === undefined) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The \"spec\" property must be a model."
                ),
                args,
                "3593d2e3851720b1f4155b5e502b0a9bf7f33512"
            );
        }

        let specChangedCallback = CBModel.valueAsFunction(
            args,
            "specChangedCallback"
        );

        if (
            useStrict &&
            specChangedCallback === undefined
        ) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The \"specChangedCallback\" argument must be a function."
                ),
                args,
                "8c4e0c27b526a1ee3d1501a75f4fcbbc944e4b18"
            );
        }

        let className = CBModel.valueToString(
            spec,
            "className"
        );

        let globalVariableName = className + "Editor";
        let editorObject;

        if (useStrict) {
            editorObject = window[globalVariableName];

            if (typeof editorObject !== "object") {
                throw Error(
                    `The ${globalVariableName} global variable is not an object`
                );
            }
        } else {
            editorObject =
            window[globalVariableName] ||
            window[className + "EditorFactory"] ||
            CBDefaultEditor;
        }

        let functionName = "CBUISpecEditor_createEditorElement";

        let createEditorElementInterface = CBModel.valueAsFunction(
            editorObject,
            functionName
        );

        if (createEditorElementInterface === undefined) {
            if (useStrict) {
                throw Error(
                    `The ${functionName}() interface has not been ` +
                    `implemented on the ${globalVariableName} object`
                );
            } else {
                /* deprecated */
                createEditorElementInterface = CBModel.valueAsFunction(
                    editorObject,
                    "createEditor"
                );
            }

        }

        let editorElement;

        if (createEditorElementInterface) {
            editorElement = createEditorElementInterface(
                {
                    spec,
                    specChangedCallback,
                }
            );
        }

        return {
            element: editorElement,
        };
    },
    /* create() */
};
/* CBUISpecEditor */
