"use strict";

var CBTextBoxViewThemeEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element                 = document.createElement("div");
        element.className           = "CBTextBoxViewThemeEditor";
        var button                  = document.createElement("button");
        button.textContent          = "Add Style";

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "title",
            spec                : args.spec
        }));

        element.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged       : args.handleSpecChanged,
            labelText               : "Styles",
            propertyName            : "styles",
            propertyUpdatedEvent    : args.spec.ID,
            spec                    : args.spec
        }));

        button.addEventListener("click", CBTextBoxViewThemeEditorFactory.handleAddStyle.bind(undefined, {
            spec                : args.spec
        }));

        element.appendChild(button);

        return element;
    },

    /**
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleAddStyle : function(args) {
        args.spec.styles = args.spec.styles.replace(/[\s]+$/, "\n\n.T" + args.spec.ID + " {\n\n}\n");
        document.dispatchEvent(new Event(args.spec.ID));
    }
};
