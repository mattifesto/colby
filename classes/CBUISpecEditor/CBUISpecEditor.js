"use strict";
/* jshint strict: global */
/* exported CBUISpecEditor */
/* global
    CBDefaultEditor */

var CBUISpecEditor = {

    /**
     * This the core function to call to create an editor for a spec. It has all
     * of the rules built into it and if an editor is being created anywhere
     * else for a spec it should be changed to call this function.
     *
     * @param object args
     *
     *      {
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
                navigateToItemCallback: args.navigateToItemCallback,
                spec: spec,
                specChangedCallback: args.specChangedCallback,
            }),
        };
    },
};
