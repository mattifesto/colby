"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageEditor */
/* global
    CBPageTemplateDescriptors,
    CBUI,
    CBUINavigationArrowPart,
    CBUINavigationView,
    CBUISectionItem4,
    CBUISpecArrayEditor,
    CBUISpecEditor,
    CBUISpecSaver,
    CBUITitleAndDescriptionPart,
    CBViewPageEditor_addableClassNames,
    CBViewPageEditor_specID,
    CBViewPageEditor_specIDToCopy,
    CBViewPageInformationEditor,
    Colby */

/**
 * The CBViewPageEditor variable allows this class to be the editor factory for
 * a CBViewPage without havng to special case it. It probably should be the name
 * of this class but we're moving toward making this a model so it's not worth
 * too much effort.
 */
var CBViewPageEditor = {

    /**
     * @deprecated use CBViewPageEditor.spec
     */
    model: undefined,

    /**
     * This variable will be set to the spec as soon as the spec is loaded or
     * a page template is selected.
     */
    spec: undefined,

    /**
     * This will be set to a function by the CBViewPageInformationEditor.
     */
    thumbnailChangedCallback: undefined,

    /**
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var editorContainer = document.createElement("div");
        editorContainer.classList.add("CBViewPageEditor");

        editorContainer.appendChild(CBUI.createHalfSpace());

        /**
         * Page information
         */

        editorContainer.appendChild(CBViewPageInformationEditor.createEditor({
            handleTitleChanged: CBViewPageEditor.handleTitleChanged.bind(undefined, { spec: args.spec }),
            makeFrontPageCallback: CBViewPageEditor.makeFrontPage.bind(undefined, { ID: args.spec.ID }),
            navigateToItemCallback: args.navigateToItemCallback,
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }));
        editorContainer.appendChild(CBUI.createHalfSpace());

        /* views */
        {
            if (args.spec.sections === undefined) {
                args.spec.sections = [];
            }

            let editor = CBUISpecArrayEditor.create({
                specs: args.spec.sections,
                specsChangedCallback: args.specChangedCallback,
                addableClassNames: CBViewPageEditor_addableClassNames,
                navigateToItemCallback: args.navigateToItemCallback,
            });

            editor.title = "Views";

            editorContainer.appendChild(editor.element);
            editorContainer.appendChild(CBUI.createHalfSpace());
        }

        CBViewPageEditor.handleTitleChanged({spec: args.spec});

        editorContainer.appendChild(CBUI.createHalfSpace());

        return editorContainer;
    },

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *      }
     *
     * @return undefined
     */
    handleTitleChanged: function(args) {
        var title = args.spec.title || "";
        title = title.trim();
        title = (title.length > 0) ? ": " + title: "";
        document.title = "Page Editor" + title;
    },

    /**
     * @param hex160 args.ID
     *
     * @return undefined
     */
    makeFrontPage: function (args) {
        if (window.confirm("Are you sure you want to use this page as the front page?")) {
            var data = new FormData();
            data.append("ID", args.ID);

            var xhr = new XMLHttpRequest();
            xhr.onerror = Colby.displayXHRError.bind(undefined, { xhr: xhr });
            xhr.onload = CBViewPageEditor.makeFrontPageDidLoad.bind(undefined, { xhr: xhr });
            xhr.open("POST", "/api/?class=CBSitePreferences&function=setFrontPageID", true);
            xhr.send(data);
        }
    },

    /**
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    makeFrontPageDidLoad: function (args) {
        Colby.displayResponse(Colby.responseFromXMLHttpRequest(args.xhr));
    },

    /**
     * @param object ajaxResponse
     *
     * @return object
     */
    saveWasFulfilled: function (ajaxResponse) {
        return ajaxResponse;
    },

    /**
     * @param Error error
     *
     * @return Promise (rejected)
     */
    saveWasRejected: function (error) {
        if (error.ajaxResponse) {
            Colby.displayResponse(error.ajaxResponse);
        } else {
            Colby.alert(error.message || "CBViewPageEditor.saveWasRejected(): No error message was provided.");
        }

        return Promise.reject(error);
    },

    /**
     * @deprecated use setThumbnailImage()
     *
     * @param object? image
     *
     * @return undefined
     */
    setThumbnail: function (image) {
        CBViewPageEditor.setThumbnailImage(image);
    },

    /**
     * @param object? image
     *  {
     *      extension: string,
     *      ID: hex160,
     *  }
     *
     * @return undefined
     */
    setThumbnailImage: function (image) {
        var spec = CBViewPageEditor.spec;

        if (spec === undefined) {
            return;
        }

        if (image === undefined) {
            spec.image = undefined;
            spec.thumbnailURL = undefined;
        } else {
            spec.image = image;
            spec.thumbnailURL = Colby.imageToURL(image, "rw640");
        }

        var callback = CBViewPageEditor.thumbnailChangedCallback;

        if (callback) {
            callback({ spec: spec, image: image });
        }

        CBViewPageEditor.specChangedCallback.call();
    },

    /**
     * @param {ID: hex160, extension: string} image
     *
     * @return undefined
     */
    suggestThumbnailImage: function (image) {
        var spec = CBViewPageEditor.spec;

        if (spec && !spec.image && !spec.thumbnailURL) {
            CBViewPageEditor.setThumbnailImage(image);
        }
    },
};
