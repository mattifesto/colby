"use strict";

var CBThemedTextViewThemeEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBThemedTextViewThemeEditor";
        var container = document.createElement("div");
        container.className = "container";
        var buttondiv = document.createElement("div");
        buttondiv.className = "buttondiv";
        var button = document.createElement("button");
        button.textContent = "Add Style";

        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "title",
            spec                : args.spec
        }));

        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged       : args.handleSpecChanged,
            labelText               : "Styles",
            propertyName            : "styles",
            propertyUpdatedEvent    : args.spec.ID,
            spec                    : args.spec
        }));

        button.addEventListener("click", CBThemedTextViewThemeEditorFactory.handleAddStyle.bind(undefined, {
            spec                : args.spec
        }));

        buttondiv.appendChild(button);
        container.appendChild(buttondiv);
        element.appendChild(container);

        return element;
    },

    /**
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleAddStyle : function(args) {
        var styles          = args.spec.styles ? args.spec.styles.trim() : "";
        var pre             = (styles !== "") ? "\n\n" : "";
        args.spec.styles    = styles.replace(/[\s]*$/, pre + "view {\n\n}\n");
        document.dispatchEvent(new Event(args.spec.ID));
    }
};
