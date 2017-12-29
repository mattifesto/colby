"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMenuEditor */
/* global
    CBUI,
    CBUISpecArrayEditor,
    CBUIStringEditor */

var CBMenuEditor = {

    /**
     * @param function args.navigateToItemCallback
     * @param Object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBMenuEditor";

        element.appendChild(CBUI.createHalfSpace());

        /* title */

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title",
            propertyName : "title",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title URI",
            propertyName : "titleURI",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        /* menu items */
        {
            if (!args.spec.items) {
                args.spec.items = [];
            }

            let editor = CBUISpecArrayEditor.create({
                addableClassNames: ["CBMenuItem"],
                navigateToItemCallback: args.navigateToItemCallback,
                specs: args.spec.items,
                specsChangedCallback: args.specChangedCallback,
            });

            editor.title = "Menu Items";

            element.appendChild(editor.element);
            element.appendChild(CBUI.createHalfSpace());
        }

        return element;
    },
};
