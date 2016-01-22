"use strict";

var CBSitePreferencesEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBSitePreferencesEditor";

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged : args.specChangedCallback,
            labelText : "Debug",
            propertyName : "debug",
            spec : args.spec,
        }));
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged : args.specChangedCallback,
            labelText : "Disallow Robots",
            propertyName : "disallowRobots",
            spec : args.spec,
        }));
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Google Tag Manager ID",
            propertyName : "googleTagManagerID",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Default Class Name for Page Settings",
            propertyName : "defaultClassNameForPageSettings",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },
};
