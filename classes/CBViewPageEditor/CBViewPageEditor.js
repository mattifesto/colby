"use strict"; /* jshint strict: global */
/* globals
    CBArrayEditor,
    CBPageEditorAvailableViewClassNames,
    CBPageTemplateDescriptors,
    CBUI,
    CBUINavigationView,
    CBUISpecEditor,
    CBUISpecSaver,
    CBURLQueryVariables,
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
    model : undefined,

    /**
     * This variable will be set to the spec as soon as the spec is loaded or
     * a page template is selected.
     */
    spec : undefined,

    /**
     * This will be set to a function by the CBViewPageInformationEditor.
     */
    thumbnailChangedCallback : undefined,

    /**
     * @return void
     */
    appendPageTemplateOption : function (template) {
        if (!CBViewPageEditor.pageTemplatesSection) {
            var mainElement = document.getElementsByTagName("main")[0];
            var pageTemplatesSection = document.createElement("section");
            pageTemplatesSection.classList.add("CBPageTemplates");
            mainElement.appendChild(pageTemplatesSection);

            CBViewPageEditor.pageTemplatesSection = pageTemplatesSection;
        }

        var pageTemplateOption = document.createElement("div");
        var pageTemplateOptionCell = document.createElement("div");
        pageTemplateOptionCell.textContent = template.title;
        pageTemplateOption.classList.add("CBPageTemplateOption");
        pageTemplateOption.appendChild(pageTemplateOptionCell);
        CBViewPageEditor.pageTemplatesSection.appendChild(pageTemplateOption);

        var handler = function () {
            /**
             * The template model will have a unique data store ID but it is
             * replaced here because the editor page has been assigned a data store
             * ID so that a reload will reload the same page instance. There may
             * be opportunity to improve clarity of this process.
             */

            var spec = JSON.parse(template.modelJSON);
            spec.ID = CBURLQueryVariables["data-store-id"];

            CBViewPageEditor.displayEditorForPageSpec(spec);
        };

        pageTemplateOption.addEventListener("click", handler, false);
    },

    /**
     * @param function args.navigateCallback (deprecated)
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    createEditor : function (args) {
        var editorContainer = document.createElement("div");
        editorContainer.classList.add("CBViewPageEditor");

        /**
         * Page information
         */

        editorContainer.appendChild(CBViewPageInformationEditor.createEditor({
            handleTitleChanged : CBViewPageEditor.handleTitleChanged.bind(undefined, { spec : args.spec }),
            makeFrontPageCallback : CBViewPageEditor.makeFrontPage.bind(undefined, { ID : args.spec.ID }),
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }));
        editorContainer.appendChild(CBUI.createHalfSpace());
        editorContainer.appendChild(CBUI.createSectionHeader({ text : "Views" }));

        if (args.spec.sections === undefined) { args.spec.sections = []; }

        editorContainer.appendChild(CBArrayEditor.createEditor({
            array : args.spec.sections,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBPageEditorAvailableViewClassNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
        }));

        CBViewPageEditor.handleTitleChanged({spec : args.spec});

        return editorContainer;
    },

    /**
     * @param object spec
     *
     * @return undefined
     */
    displayEditorForPageSpec : function (spec) {
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
        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback : CBViewPageEditor.specChangedCallback,
            rootItem : {
                element : element,
                title : "Page Editor",
            },
        });

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUISpecEditor.create({
            navigateCallback : navigationView.navigateToSpecCallback,
            navigateToItemCallback : navigationView.navigateToItemCallback,
            spec : spec,
            specChangedCallback : CBViewPageEditor.specChangedCallback,
        }).element);
        element.appendChild(CBUI.createHalfSpace());

        main.appendChild(navigationView.element);
    },

    /**
     * @return undefined
     */
    displayPageTemplateChooser : function () {
        var mainElement = document.getElementsByTagName("main")[0];
        mainElement.textContent = null;

        Object.keys(CBPageTemplateDescriptors).forEach(function (key) {
            CBViewPageEditor.appendPageTemplateOption(CBPageTemplateDescriptors[key]);
        });
    },

    /**
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleTitleChanged : function(args) {
        var title = args.spec.title || "";
        title = title.trim();
        title = (title.length > 0) ? ": " + title : "";
        document.title = "Page Editor" + title;
    },

    /**
     * @param hex160 args.ID
     *
     * @return undefined
     */
    makeFrontPage : function (args) {
        if (window.confirm("Are you sure you want to use this page as the front page?")) {
            var data = new FormData();
            data.append("ID", args.ID);

            var xhr = new XMLHttpRequest();
            xhr.onerror = Colby.displayXHRError.bind(undefined, { xhr : xhr });
            xhr.onload = CBViewPageEditor.makeFrontPageDidLoad.bind(undefined, { xhr : xhr });
            xhr.open("POST", "/api/?class=CBSitePreferences&function=setFrontPageID", true);
            xhr.send(data);
        }
    },

    /**
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    makeFrontPageDidLoad : function (args) {
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
    setThumbnail : function (image) {
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
    setThumbnailImage : function (image) {
        var spec = CBViewPageEditor.spec;

        if (spec === undefined) {
            return;
        }

        if (image === undefined) {
            spec.image = undefined;
            spec.thumbnailURL = undefined;
        } else {
            spec.image = image;
            spec.thumbnailURL = Colby.imageToURL(image);
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
    suggestThumbnailImage : function (image) {
        var spec = CBViewPageEditor.spec;

        if (spec && !spec.image && !spec.thumbnailURL) {
            CBViewPageEditor.setThumbnailImage(image);
        }
    },
};

/**
 * @return undefined
 */
CBViewPageEditor.DOMContentDidLoad = function() {
    CBViewPageEditor.fetchModel();
};

/**
 * @return void
 */
CBViewPageEditor.fetchModel = function() {
    var formData = new FormData();
    formData.append("id", CBURLQueryVariables["data-store-id"]);

    if (CBURLQueryVariables["id-to-copy"] !== undefined) {
        formData.append("id-to-copy", CBURLQueryVariables["id-to-copy"]);
    }

    var xhr = new XMLHttpRequest();
    xhr.onload = CBViewPageEditor.fetchModelDidLoad.bind(undefined, {
        xhr : xhr
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

var CBPageEditor = CBViewPageEditor; /* deprecated */

document.addEventListener("DOMContentLoaded", CBViewPageEditor.DOMContentDidLoad);
