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
        let spec = CBModel.valueAsModel(args, "spec");

        if (spec === undefined) {
            throw CBException.withError(
                TypeError("The \"spec\" argument must be a model."),
                "",
                "3593d2e3851720b1f4155b5e502b0a9bf7f33512"
            );
        }

        let useStrict = CBModel.valueToBool(args, "useStrict");
        let className = CBModel.valueToString(spec, "className");

        let editorObject;

        if (useStrict) {
            editorObject = window[className + "Editor"];
        } else {
            editorObject =
            window[className + "Editor"] ||
            window[className + "EditorFactory"] ||
            CBDefaultEditor;
        }

        let createEditorElementInterface = CBModel.valueAsFunction(
            editorObject,
            "CBUISpecEditor_createEditorElement"
        );

        if (createEditorElementInterface === undefined) {
            /* deprecated */
            createEditorElementInterface = CBModel.valueAsFunction(
                editorObject,
                "createEditor"
            );
        }

        let editorElement;

        if (createEditorElementInterface) {
            editorElement = createEditorElementInterface(
                {
                    spec: spec,
                    specChangedCallback: args.specChangedCallback,
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
