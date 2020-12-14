"use strict";
/* jshint strict: global */
/* exported CBThemeEditor */
/* global
    CBUI,
    CBUIStringEditor,
*/


var CBThemeEditor = {

    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function (args) {
        var section, item, editor;
        var element = document.createElement("div");
        element.className = "CBThemeEditor";
        var properties = [
            { name: "title", labelText: "Title" },
            { name: "classNameForKind", labelText: "Class Name for Kind" },
            { name: "classNameForTheme", labelText: "Class Name for Theme" },
            { name: "description", labelText: "Description" },
        ];

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        properties.forEach(function (property) {
            item = CBUI.createSectionItem();

            item.appendChild(CBUIStringEditor.createEditor({
                labelText: property.labelText,
                propertyName: property.name,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }).element);

            section.appendChild(item);
        });

        // styles

        item = CBUI.createSectionItem();
        editor = CBUIStringEditor.createEditor({
            labelText: "Styles",
            propertyName: "styles",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        });

        item.appendChild(editor.element);
        section.appendChild(item);

        // add style

        item = CBUI.createSectionItem();
        item.classList.add("button");
        var button = document.createElement("div");
        button.textContent = "Add Style";

        button.addEventListener("click", CBThemeEditor.handleAddStyle.bind(undefined, {
            spec: args.spec,
            updateValueCallback: editor.updateValueCallback,
        }));

        item.appendChild(button);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param object args.spec
     * @param function args.updateValueCallback
     *
     * @return  undefined
     */
    handleAddStyle: function(args) {
        var styles = args.spec.styles ? args.spec.styles.trim() : "";
        var pre = (styles !== "") ? "\n\n" : "";
        styles = styles.replace(/[\s]*$/, pre + "view {\n\n}");
        args.updateValueCallback.call(undefined, styles);
    },
};
