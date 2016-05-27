"use strict"; /* jshint strict: global */
/* globals CBUI, CBUIStringEditor */

var CBIconLinkViewEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBIconLinkViewEditor";

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Text",
            propertyName : "text",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "URL",
            propertyName : "URL",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        return element;
    },
};
