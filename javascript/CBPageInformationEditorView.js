"use strict";


/**
 * @param {Object} model
 *  Required. The page model.
 *
 * @return {Element}
 */
function createPageInformationEditorElement(args) {
    var pageModel   = args.model;
    args            = undefined;

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

    var titleControl = new CBTextControl("Title");
    titleControl.rootElement().classList.add("standard");

    titleControl.setValue(pageModel.title);
    titleControl.setAction(undefined, valueForTitleHasChanged);

    propertiesContainer.appendChild(titleControl.rootElement());


    /**
     *
     */

    propertiesContainer.appendChild(createDescriptionControlElement());


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
     * @return {Element}
     */
    function createDescriptionControlElement() {
        var descriptionControl = new CBTextControl("Description");

        descriptionControl.setValue(pageModel.description);
        descriptionControl.setAction(undefined, valueForDescriptionHasChanged);
        descriptionControl.rootElement().classList.add("standard");

        return descriptionControl.rootElement();
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
     * @param {CBTextControl} sender
     *
     * @return {undefined}
     */
    function valueForDescriptionHasChanged(sender) {
        pageModel.description       = sender.value().trim();
        pageModel.descriptionHTML   = Colby.textToHTML(pageModel.description);

        CBPageEditor.requestSave();
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
     * @param {CBTextControl} sender
     *
     * @return {undefined}
     */
    function valueForTitleHasChanged(sender) {
        pageModel.title     = sender.value().trim();
        pageModel.titleHTML = Colby.textToHTML(pageModel.title);

        if (!pageModel.URIIsStatic)
        {
            URIControl.setURI(generateURI());
            valuesForURIHaveChanged(URIControl);
        }

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
