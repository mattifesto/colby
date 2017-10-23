"use strict";
/* jshint strict: global */
/* exported CBMenuEditor */
/* global
    CBArrayEditor,
    CBUI,
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
        var element         = document.createElement("div");
        element.className   = "CBMenuEditor";

        if (!args.spec.items) {
            args.spec.items = [];
        }

        element.appendChild(CBUI.createHalfSpace());

        /* title */

        element.appendChild(CBUI.createHalfSpace());
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

        /* menu items */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({text : "Menu Items"}));

        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.items,
            arrayChangedCallback : args.specChangedCallback,
            classNames : ["CBMenuItem"],
            navigateToItemCallback : args.navigateToItemCallback,
        }));

        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
};
