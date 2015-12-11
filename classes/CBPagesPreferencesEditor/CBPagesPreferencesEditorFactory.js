"use strict";

var CBPagesPreferencesEditorFactory = {

    /**
     * @param function handleSpecChanged
     * @param object spec
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPagesPreferencesEditor";
        var properties = [
            { name : "supportedViewClassNames", labelText : "Supported View Class Names"},
            { name : "deprecatedViewClassNames", labelText : "Deprecated View Class Name"},
            { name : "classNamesForKinds", labelText : "Class Names for Kinds"},
        ];

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        properties.forEach(function (property) {
            item = CBUI.createSectionItem();

            item.appendChild(CBUIStringEditor.createEditor({
                labelText           : property.labelText,
                propertyName        : property.name,
                spec                : args.spec,
                specChangedCallback : args.handleSpecChanged,
            }).element);

            section.appendChild(item);
        });

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
};
