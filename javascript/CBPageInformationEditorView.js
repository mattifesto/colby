"use strict";

var CBPageInformationEditorFactory = {

    /**
     * @param   {Element}   checkbox
     * @param   {string}    listClassName
     *
     * @return  {undefined}
     */
    checkboxDidChangeForListClassName : function(args) {
        var checkbox        = args.checkbox;
        var listClassName   = args.listClassName;

        if (!args.spec.listClassNames)
        {
            args.spec.listClassNames = [];
        }

        var index = args.spec.listClassNames.indexOf(listClassName);

        if (checkbox.checked) {
            if (index < 0) {
                args.spec.listClassNames.push(listClassName);
            }
        } else {
            if (index >= 0) {
                args.spec.listClassNames.splice(index, 1);
            }
        }

        CBPageEditor.requestSave();
    },

    /**
     * @param   {Object}    spec
     * @param   {function}  handleSpecChanged
     * @param   {function}  handleTitleChanged
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        args.spec.URI       = args.spec.URI ? args.spec.URI : CBPageInformationEditorFactory.titleToURI({
            ID              : args.spec.dataStoreID,
            title           : args.spec.title });
        var editor          = document.createElement("section");
        editor.className    = "CBPageInformationEditor";
        var header          = document.createElement("header");
        header.textContent  = "Page Information";
        var content         = document.createElement("div");

        editor.appendChild(header);
        editor.appendChild(content);

        var propertiesContainer         = document.createElement("div");
        propertiesContainer.className   = "properties";

        content.appendChild(propertiesContainer);

        var sidebar         = document.createElement("div");
        sidebar.className   = "sidebar";

        var preview = CBImageEditorFactory.createThumbnailPreviewElement();
        var upload  = CBImageEditorFactory.createEditorUploadButton({
            handleImageUploaded : CBPageInformationEditorFactory.handleThumbnailUploaded.bind(undefined, {
                handleSpecChanged   : args.handleSpecChanged,
                previewImageElement : preview.img,
                spec                : args.spec
            }),
            imageSizes              : ["rs200clc200"]
        });

        sidebar.appendChild(preview.element);
        sidebar.appendChild(upload);

        CBImageEditorFactory.displayThumbnail({
            img : preview.img,
            URL : args.spec.thumbnailURL
        });

        sidebar.appendChild(CBPageInformationEditorFactory.createPageListsEditorElement({
            spec : args.spec
        }));

        content.appendChild(sidebar);

        /**
         *
         */

        var URIControl  = new CBPageURIControl("URI");

        URIControl.setURI(args.spec.URI);
        URIControl.setIsStatic(args.spec.URIIsStatic);
        URIControl.setIsDisabled(args.spec.isPublished);
        URIControl.setAction(undefined, CBPageInformationEditorFactory.valuesForURIHaveChanged.bind(undefined, {
            handleSpecChanged   : args.handleSpecChanged,
            spec                : args.spec
        }));

        URIControl.rootElement().classList.add("standard");

        /**
         *
         */

        propertiesContainer.appendChild(CBStringEditorFactory.createSingleLineEditor({
                handleSpecChanged   : CBPageInformationEditorFactory.handleTitleChanged.bind(undefined, {
                    handleSpecChanged   : args.handleSpecChanged,
                    handleTitleChanged  : args.handleTitleChanged,
                    spec                : args.spec,
                    URIControl          : URIControl }),
                labelText           : "Title",
                spec                : args.spec,
                propertyName        : 'title' }));

        propertiesContainer.appendChild(CBStringEditorFactory.createSingleLineEditor({
                handleSpecChanged   : args.handleSpecChanged,
                labelText           : "Description",
                spec                : args.spec,
                propertyName        : 'description' }));

        propertiesContainer.appendChild(URIControl.rootElement());

        propertiesContainer.appendChild(CBPageInformationEditorFactory.createPublicationControlElement({
            handleSpecChanged   : args.handleSpecChanged,
            spec                : args.spec,
            URIControl          : URIControl
        }));

        var flexContainer       = document.createElement("div");
        flexContainer.className = "flexContainer";

        var users = CBUsersWhoAreAdministrators.map(function(user) {
            return { textContent : user.name, value : user.ID };
        });

        flexContainer.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : users,
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Published By",
            propertyName        : "publishedBy",
            spec                : args.spec
        }));

        if (CBClassNamesForKinds.length > 0) {
            var classNames = CBClassNamesForKinds.map(function(className) {
                return { textContent : className.replace(/PageKind$/, ""), value : className };
            });

            classNames.unshift({ textContent : "None", value : "" });

            flexContainer.appendChild(CBStringEditorFactory.createSelectEditor({
                data                : classNames,
                handleSpecChanged   : args.handleSpecChanged,
                labelText           : "Kind",
                propertyName        : "classNameForKind",
                spec                : args.spec
            }));
        }

        propertiesContainer.appendChild(flexContainer);

        return editor;
    },

    /**
     * @param   {string}    listClassName
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createPageListOptionElement : function(args) {
        var listClassName   = args.listClassName;

        var container       = document.createElement("div");
        var checkbox        = document.createElement("input");
        checkbox.type       = "checkbox";
        var label           = document.createElement("label");
        label.textContent   = listClassName;

        container.appendChild(checkbox);
        container.appendChild(label);

        if (args.spec.listClassNames) {
            var index = args.spec.listClassNames.indexOf(listClassName);

            if (index >= 0) {
                checkbox.checked = true;
            }
        }

        checkbox.addEventListener("change", CBPageInformationEditorFactory.checkboxDidChangeForListClassName.bind(undefined, {
            checkbox        : checkbox,
            listClassName   : listClassName,
            spec            : args.spec
        }));

        return container;
    },

    /**
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createPageListsEditorElement : function(args) {
        var count           = CBPageEditorAvailablePageListClassNames.length;
        var element         = document.createElement("div");
        element.className   = "CBPageInformationPageListsEditor";

        for (var i = 0; i < count; i++) {
            var optionElement   = CBPageInformationEditorFactory.createPageListOptionElement({
                listClassName   : CBPageEditorAvailablePageListClassNames[i],
                spec            : args.spec
            });

            element.appendChild(optionElement);
        }

        return element;
    },

    /**
     * @param   {function}      handleSpecChanged
     * @param   {Object}        spec
     * @param   {URIControl}    URIControl
     *
     * @return  {Element}
     */
    createPublicationControlElement : function(args) {
        var control = new CBPublicationControl();

        control.setPublicationTimeStamp(args.spec.publicationTimeStamp);
        control.setIsPublished(args.spec.isPublished);
        control.setAction(undefined, CBPageInformationEditorFactory.valuesForPublicationHaveChanged.bind(undefined, {
            handleSpecChanged   : args.handleSpecChanged,
            spec                : args.spec,
            URIControl          : args.URIControl
        }));
        control.rootElement().classList.add("standard");

        return control.rootElement();
    },

    handleThumbnailUploaded : function(args, response) {
        args.spec.thumbnailURL  = response.sizes.rs200clc200.URL;

        CBImageEditorFactory.displayThumbnail({
            img : args.previewImageElement,
            URL : args.spec.thumbnailURL
        });

        args.handleSpecChanged.call();
    },

    /**
     * @param {function}    handleSpecChanged
     * @param {function}    handleTitleChanged
     * @param {Object}      spec
     * @param {Object}      URIControl
     *
     * @return {Element}
     */
    handleTitleChanged : function(args) {
        if (!args.spec.URIIsStatic) {
            args.URIControl.setURI(CBPageInformationEditorFactory.titleToURI({
                ID      : args.spec.dataStoreID,
                title   : args.spec.title }));

            args.spec.URI = args.URIControl.URI();
        }

        args.handleTitleChanged.call();
        args.handleSpecChanged.call();
    },

    /**
     * @param {string} ID
     * @param {string} title
     *
     * @return {string}
     */
    titleToURI : function(args) {
        if (args.title.length > 0) {
            return Colby.textToURI(args.title);
        } else {
            return Colby.textToURI(args.ID);
        }
    },

    /**
     * @param   {Object}        args.spec
     * @param   {function}      args.handleSpecChanged
     * @param   {URIControl}    args.URIControl
     * @param   {CBPublicationControl}  sender
     *
     * @return  {undefined}
     */
    valuesForPublicationHaveChanged : function(args, sender) {
        args.spec.isPublished = sender.isPublished();
        args.spec.publicationTimeStamp = sender.publicationTimeStamp();

        if (args.spec.isPublished)
        {
            args.spec.URIIsStatic = true;

            args.URIControl.setIsStatic(true);
        }

        args.URIControl.setIsDisabled(args.spec.isPublished);

        CBPageEditor.requestSave();
    },

    /**
     * @param   {Object}        args.spec
     * @param   {function}      args.handleSpecChanged
     * @param   {URIControl}    sender
     *
     * @return  {undefined}
     */
    valuesForURIHaveChanged : function(args, sender) {
        args.spec.URI           = sender.URI();
        args.spec.URIIsStatic   = sender.isStatic();

        args.handleSpecChanged.call();
    }
};
