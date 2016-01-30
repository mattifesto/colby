"use strict";

var CBImageViewEditor = {

    /**
     * @param {number}      cropToHeight
     * @param {number}      cropToWidth
     * @param {number}      reduceToHeight
     * @param {number}      reduceToWidth
     * @param {Object}      spec
     * @param {function}    specChangedCallback
     *
     * @return {Element}
     */
    createEditor : function(args) {
        var section, item;
        var element             = document.createElement("div");
        element.className       = "CBImageViewEditor";
        var background          = document.createElement("div");
        background.className    = "background";
        var thumbnail           = document.createElement("img");
        var dimensions          = document.createElement("div");
        dimensions.className    = "dimensions";
        dimensions.textContent  = "no image";
        var input               = document.createElement("input");
        input.type              = "file";
        input.style.display     = "none";
        var actionLink = CBUIActionLink.create({
            callback : input.click.bind(input),
            labelText : "Upload Image...",
        });

        if (!args.spec.alternativeTextViewModel) {
            args.spec.alternativeTextViewModel = { className : "CBTextView" };
        }

        background.addEventListener("click", CBImageViewEditor.handleBackgroundClicked.bind(undefined, {
            element : background }));

        thumbnail.addEventListener("load", CBImageViewEditor.handleThumbnailLoaded.bind(undefined, {
            dimensionsElement   : dimensions,
            imageElement        : thumbnail }));

        input.addEventListener("change", CBImageViewEditor.handleImageSelected.bind(undefined, {
            cropToHeight        : args.cropToHeight,
            cropToWidth         : args.cropToWidth,
            disableCallback : actionLink.disableCallback,
            enableCallback : actionLink.enableCallback,
            handleSpecChanged   : args.specChangedCallback,
            imageElement        : thumbnail,
            inputElement        : input,
            reduceToHeight      : args.reduceToHeight,
            reduceToWidth       : args.reduceToWidth,
            spec                : args.spec,
        }));

        element.appendChild(input);

        section = CBUI.createSection();

        /* thumbnail */
        item = CBUI.createSectionItem();
        background.appendChild(thumbnail);
        item.appendChild(background);
        section.appendChild(item);

        /* dimensions */
        item = CBUI.createSectionItem();
        item.appendChild(dimensions);
        section.appendChild(item);

        /* upload action */
        item = CBUI.createSectionItem();
        item.appendChild(actionLink.element);
        section.appendChild(item);

        /* alternative text */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Alternative Text",
            propertyName : "text",
            spec : args.spec.alternativeTextViewModel,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

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
     * @param function args.disableCallback
     * @param function args.enableCallback
     * @param {function}    handleSpecChanged
     * @param {Element}     imageElement
     * @param {Element}     inputElement
     * @param {number}      reduceToHeight
     * @param {number}      reduceToWidth
     * @param {Object}      spec
     *
     * @return {undefined}
     */
    handleImageSelected : function(args) {
        args.disableCallback.call();

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
        xhr.onload  = CBImageViewEditor.handleImageUploaded.bind(undefined, {
            enableCallback : args.enableCallback,
            handleSpecChanged   : args.handleSpecChanged,
            imageElement        : args.imageElement,
            spec                : args.spec,
            xhr                 : xhr });
        xhr.open("POST", "/api/?class=CBImages&function=uploadAndReduceForAjax");
        xhr.send(formData);
    },

    /**
     * @param function args.enableCallback
     * @param {function}        handleSpecChanged
     * @param {Element}         imageElement
     * @param {Object}          spec
     * @param {XMLHttpRequest}  xhr
     *
     * @return {undefined}
     */
    handleImageUploaded : function(args) {
        args.enableCallback.call();

        var response = Colby.responseFromXMLHttpRequest(args.xhr);

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
     * @param object spec
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        if (spec.alternativeTextViewModel) {
            return spec.alternativeTextViewModel.text;
        } else {
            return undefined;
        }
    },
};
