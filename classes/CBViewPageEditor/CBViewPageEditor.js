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
     * This variable will be set to the spec as soon as the editor is created.
     */
    spec: undefined,

    /**
     * This variable will be set to the specChangedCallback as soon as the
     * editor is created.
     */
    specChangedCallback: undefined,

    /**
     * This will be set to a function by the CBViewPageInformationEditor.
     */
    thumbnailChangedCallback: undefined,

    /**
     * @param object args
     *
     *      {
     *          navigateToItemCallback: function
     *          spec: model
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        CBViewPageEditor.spec = args.spec;
        CBViewPageEditor.specChangedCallback = args.specChangedCallback;

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
     * @param model? image
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
     * @param model image
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
