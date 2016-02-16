"use strict";

var CBPageListViewEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPageListViewEditor";

        section = CBUI.createSection();

        /* classNameForKind */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Class Name for Kind",
            propertyName : "classNameForKind",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* themeID */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIThemeSelector.create({
            classNameForKind : "CBPageListView",
            labelText : "Theme",
            navigateCallback : args.navigateCallback,
            propertyName : "themeID",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },

    /**
     * @param string? spec.classNameForKind
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        return spec.classNameForKind;
    },
};
