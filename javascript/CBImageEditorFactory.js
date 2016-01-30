"use strict";

var CBImageEditorFactory = {

    /**
     * This editor edits an image ID property as opposed to an image URL
     * property. An ID property is desirable when multiple versions of an image
     * are used by a view.
     *
     * @param {function} handleSpecChanged
     * @param {string} propertyName
     * @param {Object} spec
     *
     * @return {Element}
     */
    createImageIDEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBImageEditor320";
        var preview = CBImageEditorFactory.createImagePreviewElement();
        preview.element.className = "CBImageIDEditorPreview";

        element.appendChild(preview.element);
        element.appendChild(CBImageEditorFactory.createEditorUploadButton({
            handleImageUploaded : CBImageEditorFactory.handleImageUploadedForIDEditor.bind(undefined, {
                handleSpecChanged : args.handleSpecChanged,
                img : preview.img,
                propertyName : args.propertyName,
                spec : args.spec
            })
        }));

        if (args.spec[args.propertyName] !== undefined) {
            CBImageEditorFactory.displayThumbnail({
                img : preview.img,
                URL : Colby.dataStoreIDToURI(args.spec[args.propertyName].ID) + "/original." + args.spec[args.propertyName].extension
            });
        }

        return element;
    },

    /**
     * This creates an image editor that will modify a spec with the schema:
     *
     *      height  {int}
     *      width   {int}
     *      URL     {string}
     *
     * @param {string}      className
     *      By default this editor will be displayed with default styles that
     *      are generally useful in most situations. If this argument is
     *      specified, its value will be the class name on the element created
     *      here which will disable the default styles and enable the caller to
     *      use custom styles instead.
     *
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
        var section, item;
        var element             = document.createElement("div");
        element.className       = args.className || "CBImageEditor";
        var background          = document.createElement("div");
        background.className    = "background";
        var thumbnail           = document.createElement("img");
        var dimensions          = document.createElement("div");
        dimensions.className = "dimensions";
        dimensions.textContent  = "no image";
        var input               = document.createElement("input");
        input.type              = "file";
        input.style.display     = "none";
        var actionLink = CBUIActionLink.create({
            callback : input.click.bind(input),
            labelText : "Upload Image...",
        });

        background.addEventListener("click", CBImageEditorFactory.handleBackgroundClicked.bind(undefined, {
            element : background,
        }));

        thumbnail.addEventListener("load", CBImageEditorFactory.handleThumbnailLoaded.bind(undefined, {
            dimensionsElement   : dimensions,
            imageElement        : thumbnail,
        }));

        input.addEventListener("change", CBImageEditorFactory.handleImageSelected.bind(undefined, {
            cropToHeight        : args.cropToHeight,
            cropToWidth         : args.cropToWidth,
            disableCallback : actionLink.disableCallback,
            enableCallback : actionLink.enableCallback,
            handleSpecChanged   : args.handleSpecChanged,
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

        element.appendChild(section);

        if (args.spec.URL) {
            thumbnail.src = args.spec.URL;
        }

        return element;
    },

    /**
     * @deprecated use CBImageEditorFactory.createImagePreviewElement
     *
     *     Creates a CBImageEditorPreview element which is 440px by 240px and
     * has a clickable background that will change color to improve viewing of
     * translucent images.
     *     The element has a `editorPreviewSetSrc` function on it which will
     * set the `src` property of its image.
     *     You must load `CBImageEditorFactory.css` if you want to use the
     * default styles for this element.
     *
     * @param   {string}    className   Replaces the default class name
     *
     * @return  {Element}
     */
    createEditorPreview : function(args) {
        args                            = args || {};
        var preview                     = document.createElement("div");
        preview.className               = args.className || "CBImageEditorPreview";
        var img                         = document.createElement("img");
        preview.editorPreviewSetSrc     = function(URL) {
            img.src = URL;
        };

        preview.appendChild(img);

        preview.addEventListener("click", function() {
            preview.classList.toggle("dark");
        });

        return preview;
    },

    /**
     * @param   {function}  handleImageUploaded
     * @param   {Array}     imageSizes
     * @param   {string}    textContent
     *
     * @return  {Element}
     */
    createEditorUploadButton : function(args) {
        var element         = document.createElement("span");
        element.className   = "CBImageEditorUploadButton";
        var button          = document.createElement("button");
        button.textContent  = args.textContent || "Upload Image";
        var input           = document.createElement("input");
        input.type          = "file";
        input.style.display = "none";

        element.appendChild(button);
        element.appendChild(input);

        button.addEventListener("click", input.click.bind(input));
        input.addEventListener("change", CBImageEditorFactory.handleImageFileChosenForButton.bind(undefined, {
            fileInputElement    : input,
            handleImageUploaded : args.handleImageUploaded,
            imageSizes          : args.imageSizes,
            uploadButtonElement : button
        }));

        return element;
    },

    /**
     * This function returns an object containing references to its most
     * important elements. This is the most flexible way to implement this such
     * that the caller can hold onto what they need and release what they don't.
     * This should deprecate all other code that builds this same type of
     * preview element. Other code should call this function then customize the
     * behavior to meet its needs.
     *
     * You must load `CBImageEditorFactory.css` if you want to use the default
     * styles for this element.
     *
     * @return  {Object}
     *
     *      {
     *          element : {div},
     *          img     : {img},
     *          size    : {div}
     *      }
     */
    createImagePreviewElement : function() {
        var element         = document.createElement("div");
        element.className   = "CBImageEditorPreview";
        var img             = document.createElement("img");
        var size            = document.createElement("div");
        size.className      = "size";

        element.appendChild(img);
        element.appendChild(size);

        element.addEventListener("click", function() {
            element.classList.toggle("dark");
        });

        return {
            element : element,
            img     : img,
            size    : size
        };
    },

    /**
     * @return {Object}
     *
     *      {
     *          element : {div},
     *          img     : {img}
     *      }
     */
    createThumbnailPreviewElement : function() {
        var element         = document.createElement("div");
        element.className   = "CBImageEditorThumbnailPreview";
        var img             = document.createElement("img");

        element.appendChild(img);

        element.addEventListener("click", function() {
            element.classList.toggle("dark");
        });

        return {
            element : element,
            img     : img,
        };
    },

    /**
     * This method may be called to properly set the URL for the thumbnail
     * image element. It ensures that the broken image icon won't show if there
     * is no image set.
     *
     * @param   {Element}   img
     * @param   {string}    URL
     *
     * @return  undefined
     */
    displayThumbnail : function(args) {
        if (args.URL === undefined) {
            args.img.style.src          = "";
            args.img.style.visibility   = "hidden";
        } else {
            args.img.src                = args.URL;
            args.img.style.visibility   = "visible";
        }
    },

    /**
     * @param {Element} element
     *
     * @return undefined
     */
    handleBackgroundClicked : function(args) {
        args.element.classList.toggle("dark");
    },

    /**
     * @param   {Element}   fileInputElement
     * @param   {function}  handleImageUploaded
     * @param   {Array}     imageSizes
     * @param   {Element}   uploadButtonElement
     *
     * @return  undefined
     */
    handleImageFileChosenForButton : function(args) {
        args.uploadButtonElement.disabled   = true;
        var formData                        = new FormData();

        formData.append("image", args.fileInputElement.files[0]);

        args.fileInputElement.value = null;

        if (args.imageSizes) {
            formData.append("imageSizesAsJSON", JSON.stringify(args.imageSizes));
        }

        var xhr                 = new XMLHttpRequest();
        xhr.onload              = CBImageEditorFactory.handleImageUploadedForButton.bind(undefined, {
            handleImageUploaded : args.handleImageUploaded,
            uploadButtonElement : args.uploadButtonElement,
            xhr                 : xhr
        });
        xhr.open("POST", "/api/?class=CBImages&function=upload");
        xhr.send(formData);
    },

    /**
     * @deprecated use createEditorUploadButton
     *
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
     * @return undefined
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
        xhr.onload  = CBImageEditorFactory.handleImageUploaded.bind(undefined, {
            enableCallback : args.enableCallback,
            handleSpecChanged   : args.handleSpecChanged,
            imageElement        : args.imageElement,
            spec                : args.spec,
            xhr                 : xhr
        });
        xhr.open("POST", "/api/?class=CBImages&function=uploadAndReduceForAjax");
        xhr.send(formData);
    },

    /**
     * @deprecated use createEditorUploadButton
     *
     * @param {function}        handleSpecChanged
     * @param {Element}         imageElement
     * @param {Object}          spec
     * @param {Element}         uploadButtonElement
     * @param {XMLHttpRequest}  xhr
     *
     * @return undefined
     */
    handleImageUploaded : function(args) {
        args.enableCallback.call();

        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (!response.wasSuccessful) {
            Colby.displayResponse(response);
        } else {
            args.spec.height        = response.actualHeight;
            args.spec.width         = response.actualWidth;
            args.spec.URL           = response.URL;
            args.imageElement.src   = args.spec.URL;

            args.handleSpecChanged.call();
        }
    },

    /**
     * @param {function}        handleImageUploaded
     * @param {Element}         uploadButtonElement
     * @param {XMLHttpRequest}  xhr
     *
     * @return undefined
     */
    handleImageUploadedForButton : function(args) {
        args.uploadButtonElement.disabled   = false;
        var response                        = Colby.responseFromXMLHttpRequest(args.xhr);

        if (!response.wasSuccessful) {
            Colby.displayResponse(response);
        } else {
            args.handleImageUploaded.call(undefined, response);
        }
    },

    /**
     * @param {function} handleSpecChanged
     * @param {Element} img
     * @param {string} propertyName
     * @param {Object} spec
     *
     * @return undefined
     */
    handleImageUploadedForIDEditor : function(args, response) {
        args.spec[args.propertyName] = {
            extension : response.extension,
            ID : response.ID
        };

        args.handleSpecChanged.call();
        CBImageEditorFactory.displayThumbnail({
            img : args.img,
            URL : response.sizes.original.URL
        });
    },

    /**
     * @param {Element} dimensionsElement
     * @param {Element} imageElement
     *
     * @return undefined
     */
    handleThumbnailLoaded : function(args) {
        args.dimensionsElement.textContent = args.imageElement.naturalWidth + " × " + args.imageElement.naturalHeight;
    }
};
