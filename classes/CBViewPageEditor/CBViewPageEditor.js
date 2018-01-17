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

        var modelInspectorButton = CBUI.createButton({
            text: "Go to Inspector",
            callback: function () {
                window.location = "/admin/?c=CBModelInspector&ID=" + args.spec.ID;
            },
        });

        editorContainer.appendChild(modelInspectorButton.element);

        var moveToTrashButton = CBUI.createButton({
            text: 'Move to Trash',
            callback: function () {
                if (window.confirm('Are you sure you want to move this page to the trash?')) {
                    moveToTrash();
                }
            },
        });

        editorContainer.appendChild(moveToTrashButton.element);

        function moveToTrash() {
            moveToTrashButton.disable();

            Colby.callAjaxFunction("CBPages", "moveToTrash", { ID: args.spec.ID })
                .then(moveToTrashFulfilled)
                .catch(Colby.displayAndReportError)
                .then(moveToTrashFinally);
        }

        function moveToTrashFulfilled() {
            alert('The page is the trash. You will be redirected to pages administration.');

            window.location = "/admin/page/?class=CBAdminPageForPagesFind";
        }

        function moveToTrashFinally() {
            moveToTrashButton.enable();
        }

        editorContainer.appendChild(CBUI.createHalfSpace());

        return editorContainer;
    },

    /**
     * @param object spec
     *
     * @return undefined
     */
    displayEditorForPageSpec: function (spec) {
        CBViewPageEditor.model = spec;
        CBViewPageEditor.spec = spec;

        var specSaver = CBUISpecSaver.create({
            fulfilledCallback: CBViewPageEditor.saveWasFulfilled,
            rejectedCallback: CBViewPageEditor.saveWasRejected,
            spec: spec,
        });

        CBViewPageEditor.specChangedCallback = specSaver.specChangedCallback;

        var element = document.createElement("div");
        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;

        var inspectHeaderButtonItem = CBUI.createHeaderButtonItem({
            callback: function () {
                window.location = "/admin/?c=CBModelInspector&ID=" + spec.ID;
            },
            text: "Inspect",
        });

        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback: CBViewPageEditor.specChangedCallback,
            rootItem: {
                element: element,
                rightElements: [inspectHeaderButtonItem],
                title: "Page Editor",
            },
        });

        element.appendChild(CBUISpecEditor.create({
            navigateToItemCallback: navigationView.navigateToItemCallback,
            spec: spec,
            specChangedCallback: CBViewPageEditor.specChangedCallback,
        }).element);

        main.appendChild(navigationView.element);
    },

    /**
     * @return undefined
     */
    displayPageTemplateChooser: function () {
        let mainElement = document.getElementsByTagName("main")[0];
        mainElement.textContent = null;

        mainElement.appendChild(CBUI.createHalfSpace());

        let sectionElement = CBUI.createSection();

        Object.keys(CBPageTemplateDescriptors).forEach(function (key) {
            let descriptor = CBPageTemplateDescriptors[key];
            let sectionItem = CBUISectionItem4.create();
            let titlePart = CBUITitleAndDescriptionPart.create();
            titlePart.title = descriptor.title;
            let arrowPart = CBUINavigationArrowPart.create();

            sectionItem.appendPart(titlePart);
            sectionItem.appendPart(arrowPart);
            sectionItem.callback = edit;

            sectionElement.appendChild(sectionItem.element);

            function edit() {
                let spec = JSON.parse(descriptor.specAsJSON);
                spec.ID = CBViewPageEditor_specID;

                CBViewPageEditor.displayEditorForPageSpec(spec);
            }
        });

        mainElement.appendChild(sectionElement);

        mainElement.appendChild(CBUI.createHalfSpace());
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
     * @return undefined
     */
    init: function () {
        if (window.CBModelEditor_modelID === undefined) {
            // if we're not using the model editor
            CBViewPageEditor.fetchModel();
        }
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

/**
 * @return void
 */
CBViewPageEditor.fetchModel = function() {
    var formData = new FormData();
    formData.append("id", CBViewPageEditor_specID);

    if (CBViewPageEditor_specIDToCopy) {
        formData.append("id-to-copy", CBViewPageEditor_specIDToCopy);
    }

    var xhr = new XMLHttpRequest();
    xhr.onload = CBViewPageEditor.fetchModelDidLoad.bind(undefined, {
        xhr: xhr
    });
    xhr.open("POST", "/api/?class=CBViewPage&function=fetchSpec");
    xhr.send(formData);
};

/**
 * @return undefined
 */
CBViewPageEditor.fetchModelDidLoad = function(args) {
    var response = Colby.responseFromXMLHttpRequest(args.xhr);

    if (response.wasSuccessful) {
        if ("modelJSON" in response) {
            var spec = JSON.parse(response.modelJSON);

            /* Before 2016.01.21 specs did not have their className property
               set. Now the className property must be set for the page to be
               edited properly. */
            if (spec.className === undefined) {
                spec.className = "CBViewPage";
            }

            CBViewPageEditor.displayEditorForPageSpec(spec);
        } else {
            CBViewPageEditor.displayPageTemplateChooser();
        }
    } else {
        Colby.displayResponse(response);
    }
};

Colby.afterDOMContentLoaded(CBViewPageEditor.init);
