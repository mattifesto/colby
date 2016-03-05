"use strict";

var CBPagesPreferencesEditorFactory = {

    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPagesPreferencesEditor";
        var properties = [
            { name : "supportedViewClassNames", labelText : "Supported View Class Names" },
            { name : "deprecatedViewClassNames", labelText : "Deprecated View Class Name" },
            { name : "classNamesForKinds", labelText : "Class Names for Kinds" },
            { name : "classNamesForLayouts", labelText : "Class Names for Layouts" },
            { name : "classNamesForSettings", labelText : "Class Names for Page Settings" },
        ];

        section = CBUI.createSection();

        properties.forEach(function (property) {
            item = CBUI.createSectionItem();
            item.appendChild(CBUIStringEditor.createEditor({
                labelText           : property.labelText,
                propertyName        : property.name,
                spec                : args.spec,
                specChangedCallback : args.specChangedCallback,
            }).element);
            section.appendChild(item);
        });

        element.appendChild(section);

        return element;
    },
};
