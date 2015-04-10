"use strict";


/**
 * @param model
 *  The page model
 * @param handlePropertyChanged
 *  The function to call when any model property value changes.
 *
 * @return {Element}
 */
function createPageInformationEditorElement(args) {
    var pageModel   = args.model;

    var pageInformationEditorElement;

    if (!pageModel.URI)
    {
        pageModel.URI = generateURI();
    }

    pageInformationEditorElement = createRootElement();

    pageInformationEditorElement.appendChild(createHeaderElement());

    var propertiesContainer = createPropertiesContainerElement();
    pageInformationEditorElement.appendChild(propertiesContainer);

    /**
     *
     */

    pageInformationEditorElement.appendChild(createPageListsEditorElement());


    /**
     *
     */

    propertiesContainer.appendChild(createTitleField({ handlePropertyChanged: args.handlePropertyChanged }));
    propertiesContainer.appendChild(createDescriptionField({ handlePropertyChanged: args.handlePropertyChanged }));


    /**
     *
     */

    var URI         = pageModel.URI ? pageModel.URI : generateURI();
    var URIControl  = new CBPageURIControl("URI");

    URIControl.setURI(URI);
    URIControl.setIsStatic(pageModel.URIIsStatic);
    URIControl.setIsDisabled(pageModel.isPublished);
    URIControl.setAction(undefined, valuesForURIHaveChanged);

    URIControl.rootElement().classList.add("standard");
    propertiesContainer.appendChild(URIControl.rootElement());

    /**
     *
     */

    propertiesContainer.appendChild(createPublicationControlElement());


    /**
     *
     */

    var publishedByContainer = document.createElement("div");
    publishedByContainer.classList.add("container");

    propertiesContainer.appendChild(publishedByContainer);


    /**
     *
     */

    publishedByContainer.appendChild(createPublishedByControlElement());

    /**
     * No need for args in the closure after this function has run.
     */

    args = undefined;

    return pageInformationEditorElement;


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
     * @param handePropertyChanged
     *
     * @return {Element}
     */
    function createDescriptionField(args) {
        var field = CBPageEditor.textFieldBoundToProperty({
            handlePropertyChanged   : args.handlePropertyChanged,
            labelText               : "Description",
            model                   : pageModel,
            property                : 'description' });

        return field;
    }

    /**
     * @return {Element}
     */
    function createHeaderElement() {
        var header          = document.createElement("header");
        header.textContent  = "Page Information";

        return header;
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

        var args        = {checkbox: checkbox, listClassName: listClassName};
        var listener    = checkboxDidChangeForListClassName.bind(undefined, args);

        checkbox.addEventListener("change", listener);

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
    function createPropertiesContainerElement() {
        var container       = document.createElement("div");
        container.className = "CBPageInformationProperties";

        return container;
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
     * @return {Element}
     */
    function createPublishedByControlElement() {
        var control = new CBSelectionControl("Published By");

        control.rootElement().classList.add("standard");
        control.rootElement().classList.add("published-by");

        for (var i = 0; i < CBUsersWhoAreAdministrators.length; i++) {
            var user = CBUsersWhoAreAdministrators[i];

            control.appendOption(user.ID, user.name);
        }

        control.setValue(pageModel.publishedBy);
        control.setAction(undefined, valueForPublishedByHasChanged);

        return control.rootElement();
    }

    /**
     * @return {Element}
     */
    function createRootElement() {
        var rootElement = document.createElement("section");

        rootElement.classList.add("CBSectionEditorView");
        rootElement.classList.add("CBPageInformationEditorView");

        return rootElement;
    }

    /**
     * @param handlePropertyChanged
     *
     * @return Element
     */
    function createTitleField(args) {
        var handlePropertyChanged   = args.handlePropertyChanged;
        var handler                 = function() {
            if (!pageModel.URIIsStatic) {
                URIControl.setURI(generateURI());
                valuesForURIHaveChanged(URIControl);
            }

            handlePropertyChanged.call();
        }

        var field = CBPageEditor.textFieldBoundToProperty({
            handlePropertyChanged   : handler,
            labelText               : "Title",
            model                   : pageModel,
            property                : 'title' });

        args = undefined;

        return field;
    }

    /**
     * @return {string}
     */
    function generateURI() {
        var URI;

        if (pageModel.title.length > 0) {
            URI = Colby.textToURI(pageModel.title);
        } else {
            URI = pageModel.dataStoreID;
        }

        return URI;
    }

    /**
     * @param {CBSelectionControl} sender
     *
     * @return {undefined}
     */
    function valueForPublishedByHasChanged(sender) {
        pageModel.publishedBy = parseInt(sender.value(), 10);

        CBPageEditor.requestSave();
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
}
