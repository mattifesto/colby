"use strict";
/* jshint strict: global */
/* exported CBUISpecEditor */
/* global
    CBDefaultEditor */

var CBUISpecEditor = {

    /**
     * This function is the core funtion to call to create an editor for a spec.
     * It has all of the rules built into it and if an editor is being created
     * anywhere else for a spec it should be changed to call this function.
     * It's uncertain if this function should live in this file or have its own
     * class.
     *
     * @param object args
     *
     *      {
     *          navigateCallback: function (deprecated)
     *          navigateToItemCallback: function
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    create: function (args) {
        var spec = args.spec;
        var editorFactory = window[spec.className + "Editor"] ||
                            window[spec.className + "EditorFactory"] ||
                            CBDefaultEditor;

        return {
            element: editorFactory.createEditor({
                navigateCallback: args.navigateCallback, /* deprecated */
                navigateToItemCallback: args.navigateToItemCallback,
                spec: spec,
                specChangedCallback: args.specChangedCallback,
            }),
        };
    },
};
