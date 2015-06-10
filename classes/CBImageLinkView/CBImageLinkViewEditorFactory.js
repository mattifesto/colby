"use strict";

var CBImageLinkViewEditorFactory = {

    /**
     * @param   {function}  args.handleValueChanged
     * @param   {boolean}   args.initialValue
     * @param   {string}    args.labelText
     *
     * @return  {Element}
     */
    createBooleanEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBImageLinkViewBooleanEditor";
        var checkbox        = document.createElement("input");
        checkbox.type       = "checkbox";
        checkbox.checked    = !!args.initialValue;
        var label           = document.createElement("label");

        checkbox.addEventListener("change", CBImageLinkViewEditorFactory.handleCheckboxChanged.bind(undefined, {
            checkboxElement     : checkbox,
            handleValueChanged  : args.handleValueChanged
        }));

        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(args.labelText));
        element.appendChild(label);

        return element;
    },

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element             = document.createElement("div");
        element.className       = "CBImageLinkViewEditor";
        var preview             = CBImageEditorFactory.createEditorPreview();
        var dimensions          = document.createElement("div");
        dimensions.className    = "dimensions";
        dimensions.textContent  = CBImageLinkViewEditorFactory.specToDimensionsText(args.spec);
        var handler             = CBImageLinkViewEditorFactory.handleRetinaChanged.bind(undefined, {
            dimensionsElement   : dimensions,
            handleSpecChanged   : args.handleSpecChanged,
            spec                : args.spec
        });
        var retina              = CBImageLinkViewEditorFactory.createBooleanEditor({
            handleValueChanged  : handler,
            initialValue        : (args.spec.density === "2x"),
            labelText           : "Retina"
        });
        handler                 = CBImageLinkViewEditorFactory.handleImageUploaded.bind(undefined, {
            dimensionsElement   : dimensions,
            handleSpecChanged   : args.handleSpecChanged,
            previewElement      : preview,
            spec                : args.spec
        });
        var button              = CBImageEditorFactory.createEditorUploadButton({
            handleImageUploaded : handler
        });
        var alt                 = CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Alternative Text",
            propertyName        : "alt",
            spec                : args.spec
        });
        var HREF                = CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Link HREF",
            propertyName        : "HREF",
            spec                : args.spec
        });
        if (args.spec.URL) {
            preview.editorPreviewSetSrc(args.spec.URL);
        }

        element.appendChild(preview);
        element.appendChild(dimensions);
        element.appendChild(button);
        element.appendChild(retina);
        element.appendChild(alt);
        element.appendChild(HREF);

        return element;
    },

    /**
     * @param   {Element}   checkboxElement
     * @param   {function}  handleValueChanged
     *
     * @return  undefined
     */
    handleCheckboxChanged : function(args) {
        args.handleValueChanged.call(undefined, args.checkboxElement.checked);
    },

    /**
     * @param   {Element}   dimensionsElement
     * @param   {function}  handleSpecChanged
     * @param   {Element}   previewElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleImageUploaded : function(args, response) {
        args.spec.height                    = response.sizes.original.height;
        args.spec.URL                       = response.sizes.original.URL;
        args.spec.width                     = response.sizes.original.width;
        args.dimensionsElement.textContent  = CBImageLinkViewEditorFactory.specToDimensionsText(args.spec);

        args.previewElement.editorPreviewSetSrc(args.spec.URL);
        args.handleSpecChanged.call();
    },

    /**
     * @param   {Element}   args.dimensionsElement
     * @param   {function}  args.handleSpecChanged
     * @param   {Object}    args.spec
     *
     * @param   {boolean}   retina
     *
     * @return  undefined
     */
    handleRetinaChanged : function(args, retina) {
        args.spec.density                   = retina ? "2x" : "1x";
        args.dimensionsElement.textContent  = CBImageLinkViewEditorFactory.specToDimensionsText(args.spec);

        args.handleSpecChanged.call();
    },

    /**
     * @param   {Object}    spec
     *
     * @return  {string}
     */
    specToDimensionsText : function(spec) {
        if (spec.URL && spec.height && spec.width) {
            var originalDimensions = spec.width + ' × ' + spec.height;
            var retinaDimensions;

            if (spec.density === '2x') {
                retinaDimensions = Math.ceil(spec.width / 2) + ' × ' + Math.ceil(spec.height / 2);
                return retinaDimensions + ' (' + originalDimensions + ')';
            } else {
                return originalDimensions;
            }
        } else if (spec.URL) {
            return 'unknown';
        } else {
            return 'no image';
        }
    },

    /**
     * @return {string}
     */
    widgetClassName : function() {
        return "CBImageLinkViewEditorWidget";
    }
}
