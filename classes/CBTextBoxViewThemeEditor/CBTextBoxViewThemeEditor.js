"use strict";

var CBTextBoxViewThemeEditor = {

    /**
     * @param object args.spec
     * @param function args.updateStylesCallback
     *
     * @return undefined
     */
    addStyle : function (args) {
        var styles = args.spec.styles ? args.spec.styles.trim() : "";
        var pre = (styles !== "") ? "\n\n" : "";
        styles = styles.replace(/[\s]*$/, pre + "textbox {\n\n}\n");
        args.updateStylesCallback.call(undefined, styles);
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var item;
        var element = document.createElement("div");
        element.className = "CBTextBoxViewThemeEditor";
        var section = CBUI.createSection();

        /* title */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title",
            propertyName : "title",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* styles */
        item = CBUI.createSectionItem();
        var stylesEditor = CBUIStringEditor.createEditor({
            labelText : "Styles",
            propertyName : "styles",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });
        item.appendChild(stylesEditor.element);
        section.appendChild(item);

        /* add style */
        var addStyleCallback = CBTextBoxViewThemeEditor.addStyle.bind(undefined, {
            spec : args.spec,
            updateStylesCallback : stylesEditor.updateValueCallback,
        });
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback : addStyleCallback,
            labelText : "Add Style",
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },
};
