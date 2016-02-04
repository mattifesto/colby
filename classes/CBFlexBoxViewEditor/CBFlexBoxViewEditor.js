"use strict";

var CBFlexBoxViewEditor = {

    /**
     * @param function args.navigateCallback
     * @param   {Object}    spec
     * @param   {function}  specChangedCallback
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var row;
        var element             = document.createElement("div");
        element.className       = "CBFlexBoxViewEditor";
        var container           = document.createElement("div");
        container.className     = "container";
        var preview             = CBImageEditorFactory.createImagePreviewElement();
        preview.img.src         = args.spec.imageURL || "";
        var options             = document.createElement("div");
        options.className       = "options";
        var flexbox             = document.createElement("h2");
        flexbox.textContent     = "Flexbox";

        container.appendChild(preview.element);

        row                 = document.createElement("div");
        row.className       = "row";

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Div",      value : "" },
                { textContent : "Article",  value : "article" },
                { textContent : "Main",     value : "main" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Type",
            propertyName        : "type",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "pixels",
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Width",
            propertyName        : "width",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "pixels",
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Height",
            propertyName        : "height",
            spec                : args.spec
        }));

        options.appendChild(row);

        /* background section */

        var background          = document.createElement("h2");
        background.textContent  = "Background";

        options.appendChild(background);

        row                 = document.createElement("div");
        row.className       = "row";
        var clear           = document.createElement("button");
        clear.textContent   = "Clear Image";
        var size            = preview.size;
        var color           = CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Color",
            propertyName        : "backgroundColor",
            spec                : args.spec
        });
        var upload          = CBImageEditorFactory.createEditorUploadButton({
            handleImageUploaded     : CBFlexBoxViewEditor.handleImageUploaded.bind(undefined, {
                handleSpecChanged   : args.specChangedCallback,
                previewImageElement : preview.img,
                sizeElement         : size,
                spec                : args.spec
            })
        });

        clear.addEventListener("click", CBFlexBoxViewEditor.handleClearImage.bind(undefined, {
            handleSpecChanged   : args.specChangedCallback,
            previewImageElement : preview.img,
            sizeElement         : size,
            spec                : args.spec
        }));

        CBFlexBoxViewEditor.displaySize({
            sizeElement : size,
            spec        : args.spec
        });

        CBFlexBoxViewEditor.displayThumbnail({
            previewImageElement : preview.img,
            spec                : args.spec
        });

        row.appendChild(upload);
        row.appendChild(clear);
        row.appendChild(color);

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Left",     value : "left" },
                { textContent : "Center",   value : "" },
                { textContent : "Right",    value : "right" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Position X",
            propertyName        : "backgroundPositionX",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Top",      value : "" },
                { textContent : "Center",   value : "center" },
                { textContent : "Bottom",   value : "bottom" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Position Y",
            propertyName        : "backgroundPositionY",
            spec                : args.spec
        }));

        options.appendChild(row);

        row                 = document.createElement("div");
        row.className       = "row";

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "No",   value : "" },
                { textContent : "Yes",  value : "repeat" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Repeat X",
            propertyName        : "backgroundRepeatX",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "No",   value : "" },
                { textContent : "Yes",  value : "repeat" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Repeat Y",
            propertyName        : "backgroundRepeatY",
            spec                : args.spec
        }));

        options.appendChild(row);

        /* flexbox section */

        options.appendChild(flexbox);

        row                 = document.createElement("div");
        row.className       = "row";

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Row",      value : "" },
                { textContent : "Row (Reverse)",  value : "row-reverse" },
                { textContent : "Column",  value : "column" },
                { textContent : "Column (Reverse)",     value : "column-reverse" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Direction",
            propertyName        : "flexDirection",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Flex Start",       value : "" },
                { textContent : "Flex End",         value : "flex-end" },
                { textContent : "Center",           value : "center" },
                { textContent : "Space Between",    value : "space-between" },
                { textContent : "Space Around",     value : "space-around" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Justify Content",
            propertyName        : "flexJustifyContent",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Flex Start",       value : "flex-start" },
                { textContent : "Flex End",         value : "flex-end" },
                { textContent : "Center",           value : "center" },
                { textContent : "Baseline",         value : "baseline" },
                { textContent : "Stretch",          value : "" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Align Items",
            propertyName        : "flexAlignItems",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Auto",     value : "" },
                { textContent : "Start",    value : "flex-start" },
                { textContent : "End",      value : "flex-end" },
                { textContent : "Center",   value : "center" },
                { textContent : "Baseline", value : "baseline" },
                { textContent : "Stretch",  value : "stretch" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Align",
            propertyName        : "flexAlignSelf",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "flex",
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Flex",
            propertyName        : "flexFlex",
            spec                : args.spec
        }));

        row.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "No Wrap",          value : "" },
                { textContent : "Wrap",             value : "wrap" },
                { textContent : "Wrap (Reverse)",   value : "wrap-reverse" }
            ],
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Wrap",
            propertyName        : "flexWrap",
            spec                : args.spec
        }));

        options.appendChild(row);
        container.appendChild(options);
        element.appendChild(container);

        element.appendChild(CBUI.createHalfSpace());

        /* subviews */

        if (args.spec.subviews === undefined) {
            args.spec.subviews = [];
        }

        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.subviews,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBPageEditorAvailableViewClassNames,
            navigateCallback : args.navigateCallback,
        }));

        return element;
    },

    /**
     * @param   {Element}   sizeElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    displaySize : function(args) {
        if (args.spec.imageHeight === undefined || args.spec.imageWidth === undefined) {
            args.sizeElement.textContent = "no image";
        } else {
            args.sizeElement.textContent = args.spec.imageWidth + " × " + args.spec.imageHeight;
        }
    },

    /**
     * @param   {Element}   previewImageElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    displayThumbnail : function(args) {
        if (args.spec.imageURL === undefined) {
            args.previewImageElement.style.visibility   = "hidden";
        } else {
            args.previewImageElement.src                = args.spec.imageURL;
            args.previewImageElement.style.visibility   = "visible";
        }
    },

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Element}   previewImageElement
     * @param   {Element}   sizeElement
     * @param   {Object}    spec
     */
    handleClearImage : function(args) {
        args.spec.imageHeight           = undefined;
        args.spec.imageURL              = undefined;
        args.spec.imageWidth            = undefined;

        CBFlexBoxViewEditor.displaySize({
            sizeElement : args.sizeElement,
            spec        : args.spec
        });

        CBFlexBoxViewEditor.displayThumbnail({
            previewImageElement : args.previewImageElement,
            spec                : args.spec
        });

        args.specChangedCallback.call();
    },

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Element}   previewImageElement
     * @param   {Element}   sizeElement
     * @param   {Object}    spec
     */
    handleImageUploaded : function(args, response) {
        args.spec.imageHeight           = response.sizes.original.height;
        args.spec.imageURL              = response.sizes.original.URL;
        args.spec.imageWidth            = response.sizes.original.width;

        CBFlexBoxViewEditor.displaySize({
            sizeElement : args.sizeElement,
            spec        : args.spec
        });

        CBFlexBoxViewEditor.displayThumbnail({
            previewImageElement : args.previewImageElement,
            spec                : args.spec
        });

        args.specChangedCallback.call();
    },

    /**
     * @param object spec
     * @param array? spec.children
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        var description;
        var subviews = spec.subviews;

        if (Array.isArray(subviews)) {
            for (var i = 0; i < subviews.length && !description; i++) {
                description = CBArrayEditor.specToDescription(subviews[i]);
            }
        }

        return description;
    },
};
