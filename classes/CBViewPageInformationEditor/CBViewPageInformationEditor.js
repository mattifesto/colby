"use strict";

var CBViewPageInformationEditor = {

    /**
     * @param   {Element}   checkbox
     * @param   {string}    listClassName
     * @param object args.spec
     * @param function args.specChangedCallback
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

        args.specChangedCallback.call();
    },

    /**
     * @param function args.handleTitleChanged
     * @param function args.makeFrontPageCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var section, item, preview, classNames;
        var element = document.createElement("section");
        element.className = "CBViewPageInformationEditor";
        args.spec.URI = args.spec.URI ? args.spec.URI : CBViewPageInformationEditor.titleToURI({
            ID : args.spec.ID,
            title : args.spec.title,
        });

        section = CBUI.createSection();

        var URIControl  = new CBPageURIControl("URI");

        URIControl.setURI(args.spec.URI);
        URIControl.setIsStatic(args.spec.URIIsStatic);
        URIControl.setIsDisabled(args.spec.isPublished);
        URIControl.setAction(undefined, CBViewPageInformationEditor.valuesForURIHaveChanged.bind(undefined, {
            handleSpecChanged   : args.specChangedCallback,
            spec                : args.spec
        }));

        URIControl.rootElement().classList.add("standard");

        /* title */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
                labelText : "Title",
                propertyName : 'title',
                spec : args.spec,
                specChangedCallback : CBViewPageInformationEditor.handleTitleChanged.bind(undefined, {
                    handleSpecChanged : args.specChangedCallback,
                    handleTitleChanged : args.handleTitleChanged,
                    spec : args.spec,
                    URIControl : URIControl,
                }),
            }).element);
        section.appendChild(item);

        /* description */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Description",
            propertyName : 'description',
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* uri */
        item = CBUI.createSectionItem();
        item.appendChild(URIControl.rootElement());
        section.appendChild(item);

        /* publication */
        item = CBUI.createSectionItem();
        item.appendChild(CBViewPageInformationEditor.createPublicationControlElement({
            handleSpecChanged   : args.specChangedCallback,
            spec                : args.spec,
            URIControl          : URIControl
        }));
        section.appendChild(item);

        var flexContainer       = document.createElement("div");
        flexContainer.className = "flexContainer";

        var users = CBUsersWhoAreAdministrators.map(function(user) {
            return { textContent : user.name, value : user.ID };
        });

        flexContainer.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : users,
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Published By",
            propertyName        : "publishedBy",
            spec                : args.spec
        }));

        if (CBClassNamesForKinds.length > 0) {
            classNames = CBClassNamesForKinds.map(function(className) {
                return { textContent : className.replace(/PageKind$/, ""), value : className };
            });

            classNames.unshift({ textContent : "None", value : "" });

            flexContainer.appendChild(CBStringEditorFactory.createSelectEditor({
                data                : classNames,
                handleSpecChanged   : args.specChangedCallback,
                labelText           : "Kind",
                propertyName        : "classNameForKind",
                spec                : args.spec
            }));
        }

        if (CBClassNamesForSettings.length > 0) {
            classNames = CBClassNamesForSettings.map(function(className) {
                return { textContent : className.replace(/PageKind$/, ""), value : className };
            });

            classNames.unshift({ textContent : "Default", value : "" });

            flexContainer.appendChild(CBStringEditorFactory.createSelectEditor({
                data                : classNames,
                handleSpecChanged   : args.specChangedCallback,
                labelText           : "Page Settings",
                propertyName        : "classNameForSettings",
                spec                : args.spec
            }));
        }

        item = CBUI.createSectionItem();
        item.appendChild(flexContainer);
        section.appendChild(item);

        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        /* thumbnail  uploader */
        item = CBUI.createSectionItem();
        var thumbnail = document.createElement("div");
        thumbnail.className = "thumbnail";
        preview = CBImageEditorFactory.createThumbnailPreviewElement();
        var upload  = CBImageEditorFactory.createEditorUploadButton({
            handleImageUploaded : CBViewPageInformationEditor.handleThumbnailUploaded.bind(undefined, {
                handleSpecChanged   : args.specChangedCallback,
                previewImageElement : preview.img,
                spec                : args.spec
            }),
            imageSizes              : ["rs200clc200"],
            textContent             : "Upload Page Thumbnail...",
        });
        thumbnail.appendChild(preview.element);
        thumbnail.appendChild(upload);
        CBImageEditorFactory.displayThumbnail({
            img : preview.img,
            URL : args.spec.thumbnailURL
        });
        item.appendChild(thumbnail);
        section.appendChild(item);

        element.appendChild(section);

        /**
         * @deprecated use kinds
         * page lists
         */

        if (CBPageEditorAvailablePageListClassNames.length > 0) {
            element.appendChild(CBUI.createHalfSpace());

            section = CBUI.createSection();
            item = CBUI.createSectionItem();
            item.appendChild(CBViewPageInformationEditor.createPageListsEditorElement({
                spec : args.spec,
                specChangedCallback : args.specChangedCallback,
            }));
            section.appendChild(item);
            element.appendChild(section);
        }

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        /* actions */
        item = CBUI.createSectionItem();
        var actions = document.createElement("div");
        actions.className = "actions";
        preview = document.createElement("a");
        preview.href = "/admin/pages/preview/?ID=" + args.spec.ID;
        preview.textContent = "Preview";
        var useAsFrontPage = document.createElement("div");
        useAsFrontPage.textContent = "Use as Front Page";
        useAsFrontPage.addEventListener("click", args.makeFrontPageCallback);
        actions.appendChild(preview);
        actions.appendChild(useAsFrontPage);
        item.appendChild(actions);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },

    /**
     * @param   {string}    listClassName
     * @param   {Object}    spec
     * @param function args.specChangedCallback
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

        checkbox.addEventListener("change", CBViewPageInformationEditor.checkboxDidChangeForListClassName.bind(undefined, {
            checkbox : checkbox,
            listClassName : listClassName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }));

        return container;
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createPageListsEditorElement : function(args) {
        var count           = CBPageEditorAvailablePageListClassNames.length;
        var element         = document.createElement("div");
        element.className   = "pagelists";

        for (var i = 0; i < count; i++) {
            var optionElement = CBViewPageInformationEditor.createPageListOptionElement({
                listClassName : CBPageEditorAvailablePageListClassNames[i],
                spec : args.spec,
                specChangedCallback : args.specChangedCallback,
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
        control.setAction(undefined, CBViewPageInformationEditor.valuesForPublicationHaveChanged.bind(undefined, {
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
            args.URIControl.setURI(CBViewPageInformationEditor.titleToURI({
                ID      : args.spec.ID,
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
        if (args.title !== undefined && args.title.length > 0) {
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

        args.handleSpecChanged.call();
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
