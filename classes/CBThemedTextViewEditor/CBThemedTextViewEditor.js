"use strict";

var CBThemedTextViewEditor = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBThemedTextViewEditor";
        var container = document.createElement("div");
        container.className = "container";

        container.appendChild(CBThemedTextViewEditor.createThemeIDEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Theme",
            propertyName        : "themeID",
            spec                : args.spec
        }));


        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "URL",
            propertyName        : "URL",
            spec                : args.spec
        }));

        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "titleAsMarkaround",
            spec                : args.spec
        }));

        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Content",
            propertyName        : "contentAsMarkaround",
            spec                : args.spec
        }));

        element.appendChild(container);

        return element;
    },

    /**
     * @param   {function}  handleSpecChanged
     * @param   {string}    labelText
     * @param   {string}    propertyName
     * @param   {Object}    spec
     * @return  {Element}
     */
    createThemeIDEditor : function(args) {
        return CBStringEditorFactory.createSelectEditor({
            data                : CBThemedTextViewThemes,
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : args.labelText,
            propertyName        : args.propertyName,
            spec                : args.spec
        });
    },

    /**
     * @param string? args.spec.titleAsMarkaround
     *
     * @return string
     */
    specToDescription : function (args) {
        return args.spec.titleAsMarkaround || "";
    }
};
