"use strict";

var CBBackgroundViewEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createEditor : function(args) {
        CBBackgroundViewEditorFactory.prepareSpec(args.spec);

        var element             = document.createElement("div");
        element.className       = "CBBackgroundViewEditor";
        var properties          = document.createElement("div");
        properties.className    = "properties";
        var imageSpec           = {
            URL                 : args.spec.imageURL };
        var handleImageChanged  = CBBackgroundViewEditorFactory.handleImageChanged.bind(undefined, {
            handleSpecChanged   : args.handleSpecChanged,
            imageSpec           : imageSpec,
            spec                : args.spec });


        var options1            = document.createElement("div");
        options1.className      = "options options1";

        options1.appendChild(CBImageEditorFactory.createEditor({
            handleSpecChanged   : handleImageChanged,
            spec                : imageSpec }));

        properties.appendChild(options1);

        var options2            = document.createElement("div");
        options2.className      = "options options2";

        options2.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Repeat Horizontally",
            propertyName        : "imageShouldRepeatHorizontally",
            spec                : args.spec }));

        options2.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Repeat Vertically",
            propertyName        : "imageShouldRepeatVertically",
            spec                : args.spec }));

        options2.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Minimum view height is image height",
            propertyName        : "minimumViewHeightIsImageHeight",
            spec                : args.spec }));


        options2.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Background color ",
            propertyName        : "color",
            spec                : args.spec }));

        properties.appendChild(options2);

        element.appendChild(properties);

        element.appendChild(CBModelArrayEditor.createEditor({
            handleSpecChanged   : args.handleSpecChanged,
            specArray           : args.spec.children }));

        return element;
    },

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      imageSpec
     * @param {Object}      spec
     *
     * @return {undefined}
     */
    handleImageChanged : function(args) {
        args.spec.imageHeight   = args.imageSpec.height;
        args.spec.imageWidth    = args.imageSpec.width;
        args.spec.imageURL      = args.imageSpec.URL;

        args.handleSpecChanged.call();
    },

    /**
     * @return {undefined}
     */
    prepareSpec : function(spec) {
        if (!spec.children) {
            spec.children = [];
        }

        if (spec.minimumViewHeightIsImageHeight === undefined) {
            spec.minimumViewHeightIsImageHeight = true;
        }
    },

    /**
     * @return {string}
     */
    widgetClassName : function() {
        return "CBBackgroundViewEditorWidget";
    }
};
