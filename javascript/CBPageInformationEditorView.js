"use strict";

var CBPageInformationEditorFactory = {

    /**
     * @param model
     *  The page model
     * @param handlePropertyChanged
     *  The function to call when any model property value changes.
     *
     * @return {Element}
     */
    createEditor : function(args) {
        var pageModel       = args.model; /* deprecated, use spec */
        var spec            = pageModel;
        pageModel.URI       = pageModel.URI ? pageModel.URI : CBPageInformationEditorFactory.titleToURI({
            ID              : pageModel.dataStoreID,
            title           : pageModel.title });
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
        content.appendChild(createPageListsEditorElement());

        /**
         *
         */

        var URIControl  = new CBPageURIControl("URI");

        URIControl.setURI(pageModel.URI);
        URIControl.setIsStatic(pageModel.URIIsStatic);
        URIControl.setIsDisabled(pageModel.isPublished);
        URIControl.setAction(undefined, valuesForURIHaveChanged);

        URIControl.rootElement().classList.add("standard");

        /**
         *
         */

        propertiesContainer.appendChild(CBStringEditorFactory.createSingleLineEditor({
                handleSpecChanged   : CBPageInformationEditorFactory.handleTitleChanged.bind(undefined, {
                    handleSpecChanged   : args.handlePropertyChanged,
                    spec                : pageModel,
                    URIControl          : URIControl }),
                labelText           : "Title",
                spec                : pageModel,
                propertyName        : 'title' }));

        propertiesContainer.appendChild(CBStringEditorFactory.createSingleLineEditor({
                handleSpecChanged   : args.handlePropertyChanged,
                labelText           : "Description",
                spec                : pageModel,
                propertyName        : 'description' }));

        propertiesContainer.appendChild(URIControl.rootElement());

        propertiesContainer.appendChild(createPublicationControlElement());

        var flexContainer       = document.createElement("div");
        flexContainer.className = "flexContainer";

        var users = CBUsersWhoAreAdministrators.map(function(user) {
            return { textContent : user.name, value : user.ID };
        });

        flexContainer.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : users,
            handleSpecChanged   : args.handlePropertyChanged,
            labelText           : "Published By",
            propertyName        : "publishedBy",
            spec                : spec
        }));

        if (CBClassNamesForKinds.length > 0) {
            var classNames = CBClassNamesForKinds.map(function(className) {
                return { textContent : className.replace(/PageKind$/, ""), value : className };
            });

            classNames.unshift({ textContent : "None", value : "" });

            flexContainer.appendChild(CBStringEditorFactory.createSelectEditor({
                data                : classNames,
                handleSpecChanged   : args.handlePropertyChanged,
                labelText           : "Kind",
                propertyName        : "classNameForKind",
                spec                : spec
            }));
        }

        propertiesContainer.appendChild(flexContainer);

        /* No need for args in the closure after this function has run. */
        args = undefined;

        return editor;


        /**
         * @param {Element} checkbox
         * @param {string} listClassName
         *
         * @return {undefined}
         */
        function checkboxDidChangeForListClassName(args) {
            var checkbox        = args.checkbox;
            var listClassName   = args.listClassName;

            if (!pageModel.listClassNames)
            {
                pageModel.listClassNames = [];
            }

            var index = pageModel.listClassNames.indexOf(listClassName);

            if (checkbox.checked) {
                if (index < 0) {
                    pageModel.listClassNames.push(listClassName);
                }
            } else {
                if (index >= 0) {
                    pageModel.listClassNames.splice(index, 1);
                }
            }

            CBPageEditor.requestSave();
        }

        /**
         * @param {string} listClassName
         *
         * @return {Element}
         */
        function createPageListOptionElement(args) {
            var listClassName   = args.listClassName;

            var container       = document.createElement("div");
            var checkbox        = document.createElement("input");
            checkbox.type       = "checkbox";
            var label           = document.createElement("label");
            label.textContent   = listClassName;

            container.appendChild(checkbox);
            container.appendChild(label);

            if (pageModel.listClassNames) {
                var index = pageModel.listClassNames.indexOf(listClassName);

                if (index >= 0) {
                    checkbox.checked = true;
                }
            }

            checkbox.addEventListener("change", checkboxDidChangeForListClassName.bind(undefined, {
                checkbox        : checkbox,
                listClassName   : listClassName }));

            return container;
        }

        /**
         * @return {Element}
         */
        function createPageListsEditorElement() {
            var count           = CBPageEditorAvailablePageListClassNames.length;
            var element         = document.createElement("div");
            element.className   = "CBPageInformationPageListsEditor";

            for (var i = 0; i < count; i++) {
                var args            = {listClassName: CBPageEditorAvailablePageListClassNames[i]};
                var optionElement   = createPageListOptionElement(args);

                element.appendChild(optionElement);
            }

            return element;
        }

        /**
         * @return {Element}
         */
        function createPublicationControlElement() {
            var control = new CBPublicationControl();

            control.setPublicationTimeStamp(pageModel.publicationTimeStamp);
            control.setIsPublished(pageModel.isPublished);
            control.setAction(undefined, valuesForPublicationHaveChanged);
            control.rootElement().classList.add("standard");

            return control.rootElement();
        }

        /**
         * @param {CBPublicationControl} sender
         *
         * @return {undefined}
         */
        function valuesForPublicationHaveChanged(sender) {
            pageModel.isPublished = sender.isPublished();
            pageModel.publicationTimeStamp = sender.publicationTimeStamp();

            if (pageModel.isPublished)
            {
                pageModel.URIIsStatic = true;

                URIControl.setIsStatic(true);
            }

            URIControl.setIsDisabled(pageModel.isPublished);

            CBPageEditor.requestSave();
        }

        /**
         * @param {URIControl} sender
         *
         * @return {undefined}
         */
        function valuesForURIHaveChanged(sender) {
            pageModel.URI           = sender.URI();
            pageModel.URIIsStatic   = sender.isStatic();

            CBPageEditor.requestSave();
        }
    },

    /**
     * @param {function}    handlesSpecChanged
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
    }
};
