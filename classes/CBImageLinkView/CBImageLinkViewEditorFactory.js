"use strict";

var CBImageLinkViewEditorFactory = {

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
        var handler             = CBImageLinkViewEditorFactory.handleImageUploaded.bind(undefined, {
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
        element.appendChild(alt);
        element.appendChild(HREF);

        return element;
    },

    /**
     * @param   {Element}   dimensionsElement
     * @param   {function}  handleSpecChanged
     * @param   {Element}   previewElement
     * @param   {Object}    spec
     *
     * @return undefined
     */
    handleImageUploaded : function(args, response) {
        args.spec.height    = response.sizes.original.height;
        args.spec.URL       = response.sizes.original.URL;
        args.spec.width     = response.sizes.original.width;

        args.previewElement.editorPreviewSetSrc(args.spec.URL);
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
