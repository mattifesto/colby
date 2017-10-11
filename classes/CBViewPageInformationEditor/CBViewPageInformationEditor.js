"use strict";
/* jshint strict: global */
/* globals
    CBCurrentUserID,
    CBPageClassNamesForKinds,
    CBPageClassNamesForLayouts,
    CBPageClassNamesForSettings,
    CBPageURIControl,
    CBPublicationControl,
    CBUI,
    CBUIActionLink,
    CBUIImageChooser,
    CBUISelector,
    CBUISpecPropertyEditor,
    CBUIStringEditor,
    CBUsersWhoAreAdministrators,
    CBViewPageEditor,
    CBViewPageInformationEditor_mainMenuItemOptions,
    Colby */

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

        /* publishedBy */
        if (!args.spec.publishedBy) {
            args.spec.publishedBy = CBCurrentUserID;
        }

        var users = CBUsersWhoAreAdministrators.map(function(user) {
            return { title : user.name, value : user.ID };
        });

        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Published By",
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            propertyName : "publishedBy",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            options : users,
        }).element);
        section.appendChild(item);

        /* classNameForSettings */
        if (CBPageClassNamesForSettings.length > 0) {
            classNames = CBPageClassNamesForSettings.map(function(className) {
                return { title : className, value : className };
            });

            classNames.unshift({ title : "Default", value : undefined });

            item = CBUI.createSectionItem();
            item.appendChild(CBUISelector.create({
                labelText : "Page Settings",
                navigateCallback : args.navigateCallback,
                navigateToItemCallback : args.navigateToItemCallback,
                propertyName : "classNameForSettings",
                spec : args.spec,
                specChangedCallback : args.specChangedCallback,
                options : classNames,
            }).element);
            section.appendChild(item);
        }

        /* classNameForKind */
        if (CBPageClassNamesForKinds.length > 0) {
            classNames = CBPageClassNamesForKinds.map(function(className) {
                return { title : className, value : className };
            });

            classNames.unshift({ title : "None", value : undefined });

            item = CBUI.createSectionItem();
            item.appendChild(CBUISelector.create({
                labelText : "Page Kind",
                navigateCallback : args.navigateCallback,
                navigateToItemCallback : args.navigateToItemCallback,
                propertyName : "classNameForKind",
                spec : args.spec,
                specChangedCallback : args.specChangedCallback,
                options : classNames,
            }).element);
            section.appendChild(item);
        }

        /* selectedMainMenuItemName */
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText: "Selected Main Menu Item",
            navigateCallback: args.navigateCallback,
            options: CBViewPageInformationEditor_mainMenuItemOptions,
            propertyName: "selectedMainMenuItemName",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        /* thumbnail */

        section = CBUI.createSection();

        var thumbnailChosenCallback = CBViewPageInformationEditor.handleThumbnailChosen;
        var thumbnailRemovedCallback = CBViewPageInformationEditor.handleThumbnailRemoved;
        var chooser = CBUIImageChooser.createThumbnailSizedChooser({
            imageChosenCallback : thumbnailChosenCallback,
            imageRemovedCallback : thumbnailRemovedCallback,
        });

        CBViewPageEditor.thumbnailChangedCallback = CBViewPageInformationEditor.handleThumbnailChanged.bind(undefined, {
            setImageURLCallback : chooser.setImageURLCallback,
        });

        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        var imageURL = args.spec.thumbnailURL; /* deprecated */

        if (args.spec.image) {
            imageURL = Colby.imageToURL(args.spec.image, "rw320");
        }

        chooser.setImageURLCallback(imageURL);

        /* actions */

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            labelText: "Preview",
            callback: function () {
                var name = "preview_" + args.spec.ID;
                window.open("/admin/pages/preview/?ID=" + args.spec.ID, name);
            },
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            labelText: "Use as Front Page",
            callback: args.makeFrontPageCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        /* layout */

        if (CBPageClassNamesForLayouts.length > 0) {
            element.appendChild(CBUI.createHalfSpace());

            var options = CBPageClassNamesForLayouts.map(function(className) {
                return { title : className, value : className };
            });

            options.unshift({ title : "None", value : undefined });

            var editor = CBUISpecPropertyEditor.create({
                labelText : "Layout",
                navigateToItemCallback : args.navigateToItemCallback,
                options : options,
                propertyName : "layout",
                spec : args.spec,
                specChangedCallback : args.specChangedCallback,
            });

            element.appendChild(editor.element);
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

    /**
     * All this function does is set the uploader thumbnail image. It may be
     * able to be replaced with a bound setImageURL callback. I think it's only
     * called by this file. Also we probably don't have to handle the
     * thumbnailURL case anymore.
     *
     * @param function args.setImageURLCallback
     * @param object pageArgs.spec
     * @param object pageArgs.image
     */
    handleThumbnailChanged : function (args, pageArgs) {
        if (pageArgs.image) {
            args.setImageURLCallback(Colby.imageToURL(pageArgs.image, "rw320"));
        } else if (pageArgs.spec.thumbnailURL) {
            args.setImageURLCallback(pageArgs.spec.thumbnailURL);
        } else {
            args.setImageURLCallback();
        }
    },

    /**
     * @param file chooserArgs.file
     * @param function chooserArgs.setImageURLCallback
     *
     * @return undefined
     */
    handleThumbnailChosen : function (chooserArgs) {
        var formData = new FormData();
        formData.append("image", chooserArgs.file);

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBViewPageInformationEditor.handleThumbnailChosenDidLoad.bind(undefined, {xhr:xhr});
        xhr.open("POST", "/api/?class=CBImages&function=upload");
        xhr.send(formData);
    },

    /**
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleThumbnailChosenDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            CBViewPageEditor.setThumbnailImage(response.image);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @return undefined
     */
    handleThumbnailRemoved : function () {
        CBViewPageEditor.setThumbnailImage();
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
     * @param hex160 ID
     * @param string title
     *
     * @return string
     */
    titleToURI: function (args) {
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
    },
};
