"use strict";

var CBMenuItemEditor = {

    /**
     * @param Object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBMenuItemEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(CBUIStringEditor.createEditor({
            labelText           : "Name",
            propertyName        : "name",
            spec                : args.spec,
            specChangedCallback : args.specChangedCallback
        }).element);

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(CBUIStringEditor.createEditor({
            labelText           : "Text",
            propertyName        : "text",
            spec                : args.spec,
            specChangedCallback : args.specChangedCallback
        }).element);

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(CBUIStringEditor.createEditor({
            labelText           : "URL",
            propertyName        : "URL",
            spec                : args.spec,
            specChangedCallback : args.specChangedCallback
        }).element);

        section.appendChild(item);
        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },

    /**
     * @param Object args.spec
     *
     * @return string
     */
    specToDescription : function (args) {
        return args.spec.text || "";
    },
};
