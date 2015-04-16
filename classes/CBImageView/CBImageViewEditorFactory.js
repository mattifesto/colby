"use strict";

var CBImageViewEditorFactory = {

    /**
     * @param {number}      cropToHeight
     * @param {number}      cropToWidth
     * @param {function}    handleSpecChanged
     * @param {number}      reduceToHeight
     * @param {number}      reduceToWidth
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createEditor : function(args) {
        CBImageViewEditorFactory.prepareSpec(args.spec);

        var element             = document.createElement("div");
        element.className       = "CBImageViewEditor";
        var background          = document.createElement("div");
        background.className    = "background";
        var thumbnail           = document.createElement("img");
        var dimensions          = document.createElement("div");
        dimensions.textContent  = "no image";
        var button              = document.createElement("button");
        button.textContent      = "Upload Image";
        var input               = document.createElement("input");
        input.type              = "file";
        input.style.display     = "none";

        background.addEventListener("click", CBImageViewEditorFactory.handleBackgroundClicked.bind(undefined, {
            element : background }));

        thumbnail.addEventListener("load", CBImageViewEditorFactory.handleThumbnailLoaded.bind(undefined, {
            dimensionsElement   : dimensions,
            imageElement        : thumbnail }));

        button.addEventListener("click", input.click.bind(input));

        input.addEventListener("change", CBImageViewEditorFactory.handleImageSelected.bind(undefined, {
            cropToHeight        : args.cropToHeight,
            cropToWidth         : args.cropToWidth,
            handleSpecChanged   : args.handleSpecChanged,
            imageElement        : thumbnail,
            inputElement        : input,
            reduceToHeight      : args.reduceToHeight,
            reduceToWidth       : args.reduceToWidth,
            spec                : args.spec,
            uploadButtonElement : button }));

        background.appendChild(thumbnail);
        element.appendChild(background);
        element.appendChild(dimensions);
        element.appendChild(button);
        element.appendChild(input);

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Alternative Text",
            propertyName        : "text",
            spec                : args.spec.alternativeTextViewModel }));

        if (args.spec.URL) {
            thumbnail.src = args.spec.URL;
        }

        return element;
    },

    /**
     * @param {Element} element
     *
     * @return {undefined}
     */
    handleBackgroundClicked : function(args) {
        args.element.classList.toggle("dark");
    },

    /**
     * @param {number}      cropToHeight
     * @param {number}      cropToWidth
     * @param {function}    handleSpecChanged
     * @param {Element}     imageElement
     * @param {Element}     inputElement
     * @param {number}      reduceToHeight
     * @param {number}      reduceToWidth
     * @param {Object}      spec
     * @param {Element}     uploadButtonElement
     *
     * @return {undefined}
     */
    handleImageSelected : function(args) {
        args.uploadButtonElement.disabled   = true;
        var formData                        = new FormData();

        formData.append("image", args.inputElement.files[0]);

        if (args.cropToHeight) {
            formData.append("cropToHeight", args.cropToHeight);
        }

        if (args.cropToWidth) {
            formData.append("cropToWidth", args.cropToWidth);
        }

        if (args.reduceToHeight) {
            formData.append("reduceToHeight", args.reduceToHeight);
        }

        if (args.reduceToWidth) {
            formData.append("reduceToWidth", args.reduceToWidth);
        }

        var xhr     = new XMLHttpRequest();
        xhr.onload  = CBImageViewEditorFactory.handleImageUploaded.bind(undefined, {
            handleSpecChanged   : args.handleSpecChanged,
            imageElement        : args.imageElement,
            spec                : args.spec,
            uploadButtonElement : args.uploadButtonElement,
            xhr                 : xhr });
        xhr.open("POST", "/api/?class=CBImages&function=uploadAndReduceForAjax");
        xhr.send(formData);
    },

    /**
     * @param {function}        handleSpecChanged
     * @param {Element}         imageElement
     * @param {Object}          spec
     * @param {Element}         uploadButtonElement
     * @param {XMLHttpRequest}  xhr
     *
     * @return {undefined}
     */
    handleImageUploaded : function(args) {
        args.uploadButtonElement.disabled   = false;
        var response                        = Colby.responseFromXMLHttpRequest(args.xhr);

        if (!response.wasSuccessful) {
            Colby.displayResponse(response);
        } else {
            args.spec.actualHeight  = response.actualHeight;
            args.spec.actualWidth   = response.actualWidth;
            args.spec.filename      = response.filename;
            args.spec.URL           = response.URL;
            args.imageElement.src   = args.spec.URL;

            args.handleSpecChanged.call();
        }
    },

    /**
     * @param {Element} dimensionsElement
     * @param {Element} imageElement
     *
     * @return {undefined}
     */
    handleThumbnailLoaded : function(args) {
        args.dimensionsElement.textContent = args.imageElement.naturalWidth + " × " + args.imageElement.naturalHeight;
    },

    /**
     * Ensures that the spec has some necessary properties.
     *
     * @return {undefined}
     */
    prepareSpec : function(spec) {
        if (!spec.alternativeTextViewModel) {
            spec.alternativeTextViewModel = { className : "CBTextView" };
        }
    },

    /**
     * @return {string}
     */
    widgetClassName : function() {
        return "CBImageViewEditorWidget";
    }
};
