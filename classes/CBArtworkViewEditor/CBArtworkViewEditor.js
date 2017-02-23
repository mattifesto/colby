"use strict"; /* jshint strict: global */
/* globals
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUIStringEditor,
    CBViewPageEditor,
    Colby */

var CBArtworkViewEditor = {

    /**
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBArtworkViewEditor";

        section = CBUI.createSection();

        var imageChosenCallback = CBArtworkViewEditor.handleImageChosen.bind(undefined, {
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });
        var imageRemovedCallback = CBArtworkViewEditor.handleImageRemoved.bind(undefined, {
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });
        var chooser = CBUIImageChooser.createThumbnailSizedChooser({
            imageChosenCallback : imageChosenCallback,
            imageRemovedCallback : imageRemovedCallback,
        });

        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Alternative Text",
            propertyName : "alternativeText",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Caption",
            propertyName : "captionAsMarkdown",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Maximum Display Width",
            navigateCallback: args.navigateCallback,
            navigateToItemCallback: args.navigateToItemCallback,
            options: [
                {title: "160 CSS pixels", value: "rw320"},
                {title: "320 CSS pixels", value: "rw640"},
                {title: "480 CSS pixels", value: "rw960"},
                {title: "640 CSS pixels", value: "rw1280"},
                {title: "800 CSS pixels (default)"},
                {title: "960 CSS pixels", value: "rw1920"},
                {title: "1280 CSS pixels", value: "rw2560"},
                {title: "Image Width", description: "The maximum width in CSS pixels is half the count of horizontal pixels of the uploaded image.", value: "original"},
                {title: "Page Width", description: "The uploaded image will always use the full width of the page regardless of its size.", value: "page"},
            ],
            propertyName : "size",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        if (args.spec.image) {
            chooser.setImageURLCallback(CBArtworkViewEditor.imageToURL(args.spec.image));
        }

        return element;
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param file chooserArgs.file
     * @param function chooserArgs.setImageURLCallback
     *
     * @return undefined
     */
    handleImageChosen : function (args, chooserArgs) {
        var formData = new FormData();
        formData.append("image", chooserArgs.file);

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBArtworkViewEditor.handleImageChosenDidLoad.bind(undefined, {
            setImageURLCallback : chooserArgs.setImageURLCallback,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBImages&function=upload");
        xhr.send(formData);
    },

    /**
     * @param function args.setImageURLCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleImageChosenDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.spec.image = {
                extension : response.image.extension,
                filename : response.image.filename,
                height : response.image.height,
                ID : response.ID,
                width : response.image.width,
            };

            args.specChangedCallback();

            args.setImageURLCallback(CBArtworkViewEditor.imageToURL(args.spec.image));

            CBViewPageEditor.suggestThumbnailImage(response.image);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param function chooserArgs.setImageURLCallback
     *
     * @return undefined
     */
    handleImageRemoved : function (args, chooserArgs) {
        args.spec.image = undefined;

        args.specChangedCallback();

        chooserArgs.setImageURLCallback();
    },

    /**
     * @deprecated use Colby.imageToURL()
     *
     * @param string image.filename
     * @param string image.extension
     * @param hex160 image.ID
     *
     * @return string
     */
    imageToURL : function (image) {
        return Colby.imageToURL(image);
    },

    /**
     * @param string? spec.alternativeText
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        return spec.alternativeText;
    },
};
