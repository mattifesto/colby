"use strict";

var CBUISpecEditor = {

    /**
     * This function is the core funtion to call to create an editor for a spec.
     * It has all of the rules built into it and if an editor is being created
     * anywhere else for a spec it should be changed to call this function.
     * It's uncertain if this function should live in this file or have its own
     * class.
     *
     * @param function args.navigateCallback (deprecated)
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return {
     *  Element element,
     * }
     */
    create : function (args) {
        var spec = args.spec;
        var editorFactory = window[spec.className + "Editor"] ||
                            window[spec.className + "EditorFactory"] ||
                            CBDefaultEditor;

        return {
            element : editorFactory.createEditor({
                navigateCallback : args.navigateCallback, /* deprecated */
                navigateToElementCallback : args.navigateToItemCallback,
                spec : spec,
                specChangedCallback : args.specChangedCallback,
            }),
        };
    },
};
