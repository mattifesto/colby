"use strict";

var CBThemedTextViewEditor = {

    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBThemedTextViewEditor";

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBThemedTextViewEditor.createThemeIDEditor({
            handleSpecChanged : args.specChangedCallback,
            labelText : "Theme",
            propertyName : "themeID",
            spec : args.spec
        }));
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "URL",
            propertyName : "URL",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title",
            propertyName : "titleAsMarkaround",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Content",
            propertyName : "contentAsMarkaround",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },

    /**
     * @param function args.handleSpecChanged
     * @param string args.labelText
     * @param string args.propertyName
     * @param object args.spec
     *
     * @return Element
     */
    createThemeIDEditor : function(args) {
        return CBStringEditorFactory.createSelectEditor({
            data : CBThemedTextViewThemes,
            handleSpecChanged : args.handleSpecChanged,
            labelText : args.labelText,
            propertyName : args.propertyName,
            spec : args.spec,
        });
    },

    /**
     * @param string? spec.titleAsMarkaround
     *
     * @return string
     */
    specToDescription : function (spec) {
        return spec.titleAsMarkaround;
    }
};
