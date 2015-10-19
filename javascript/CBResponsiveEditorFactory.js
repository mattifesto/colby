"use strict";

var CBResponsiveEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {string}    labelText
     * @param   {string}    propertyName
     * @param   {string}    propertyUpdatedEvent
     *      If the creator of this editor needs to update the property outside
     *      the editor it passes in an event which it will use to let the
     *      editor know the property has been updated. Will be replaced with
     *      Object.observe()
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

        textArea.addEventListener("input", CBResponsiveEditorFactory.handleInput.bind(undefined, {
            element             : textArea,
            handleSpecChanged   : args.handleSpecChanged,
            propertyName        : args.propertyName,
            spec                : args.spec }));

        textArea.addEventListener("input", CBResponsiveEditorFactory.resizeTextArea.bind(undefined, {
            textAreaElement : textArea
        }));

        element.appendChild(label);
        element.appendChild(textArea);

        if (args.propertyUpdatedEvent) {
            document.addEventListener(args.propertyUpdatedEvent, CBResponsiveEditorFactory.handleStringPropertyUpdated.bind(undefined, {
                textAreaElement : textArea,
                handleSpecChanged : args.handleSpecChanged,
                propertyName : args.propertyName,
                spec : args.spec
            }));
        }

        window.setTimeout(CBResponsiveEditorFactory.resizeTextArea.bind(undefined, {
            textAreaElement : textArea
        }), 0);

        /**
         * @NOTE 2015.09.24
         * There is a bug in calculating the scrollHeight of a textarea above
         * that seems to resolve itself after some time. So we recalculate.
         * The one above always either gets it right or close, so it's good to
         * have both, until the bug is fixed.
         */
        window.setTimeout(CBResponsiveEditorFactory.resizeTextArea.bind(undefined, {
            textAreaElement : textArea
        }), 1000);

        return element;
    },

    /**
     * @param   {Element}   element
     * @param   {function}  handleSpecChanged
     * @param   {string}    propertyName
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleInput : function(args) {
        args.spec[args.propertyName] = args.element.value;

        args.handleSpecChanged.call();
    },

    /**
     * @param   {Element}   textAreaElement
     * @param   {function}  handleSpecChanged
     * @param   {string}    propertyName
     * @param   {Object}    spec
     */
    handleStringPropertyUpdated : function(args) {
        args.textAreaElement.value = args.spec[args.propertyName];
        args.handleSpecChanged.call();
        CBResponsiveEditorFactory.resizeTextArea({
            textAreaElement : args.textAreaElement
        });
    },

    /**
     * @param {Element} textAreaElement
     *
     * @return undefined
     */
    resizeTextArea : function(args) {
        args.textAreaElement.style.height = "0";
        args.textAreaElement.style.height = args.textAreaElement.scrollHeight + "px";

        //alert(args.textArea.style.height);
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBResponsiveEditorFactory.css"

    document.head.appendChild(link);
})();
