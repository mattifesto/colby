"use strict";

var CBResponsiveEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {string}    labelText
     * @param   {string}    propertyName
     * @param   {Object}    spec
     *
     * @return {Element}
     */
    createStringEditorWithTextArea : function(args) {
        var ID              = Colby.random160();
        var element         = document.createElement("div");
        element.className   = "CBStringEditorWithTextArea";
        var label           = document.createElement("label");
        label.htmlFor       = ID;
        label.textContent   = args.labelText ?  args.labelText + ":" : "";
        var textArea        = document.createElement("textarea");
        textArea.id         = ID;
        textArea.value      = args.spec[args.propertyName] || "";

        textArea.addEventListener("input", CBStringEditorFactory.handleInput.bind(undefined, {
            element             : textArea,
            handleSpecChanged   : args.handleSpecChanged,
            propertyName        : args.propertyName,
            spec                : args.spec }));

        textArea.addEventListener("input", CBResponsiveEditorFactory.resizeTextArea.bind(undefined, {
            textArea : textArea
        }));

        element.appendChild(label);
        element.appendChild(textArea);

        window.setTimeout(CBResponsiveEditorFactory.resizeTextArea.bind(undefined, {
            textArea : textArea
        }), 0);

        /**
         * @NOTE 2015.09.24
         * There is a bug in calculating the scrollHeight of a textarea above
         * that seems to resolve itself after some time. So we recalculate.
         * The one above always either gets it right or close, so it's good to
         * have both, until the bug is fixed.
         */
        window.setTimeout(CBResponsiveEditorFactory.resizeTextArea.bind(undefined, {
            textArea : textArea
        }), 1000);

        return element;
    },

    /**
     * @param   {Element}   textArea
     *
     * @return  undefined
     */
    resizeTextArea : function(args) {
        args.textArea.style.height = "0";
        args.textArea.style.height = args.textArea.scrollHeight + "px";

        //alert(args.textArea.style.height);
    }
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBResponsiveEditorFactory.css"

    document.head.appendChild(link);
})();
