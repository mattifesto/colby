"use strict";

var CBThemedTextViewEditorFactory = {

    themes          : [],
    themesUpdated   : "CBThemedTextViewEditorThemesUpdated",

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBThemedTextViewEditor";
        var container = document.createElement("div");
        container.className = "container";

        container.appendChild(CBThemedTextViewEditorFactory.createThemeIDEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Theme",
            propertyName        : "themeID",
            spec                : args.spec
        }));


        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "URL",
            propertyName        : "URL",
            spec                : args.spec
        }));

        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "titleAsMarkaround",
            spec                : args.spec
        }));

        container.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Content",
            propertyName        : "contentAsMarkaround",
            spec                : args.spec
        }));

        element.appendChild(container);

        return element;
    },

    /**
     * @param   {function}  handleSpecChanged
     * @param   {string}    labelText
     * @param   {string}    propertyName
     * @param   {Object}    spec
     * @return  {Element}
     */
    createThemeIDEditor : function(args) {
        return CBStringEditorFactory.createSelectEditor({
            data                : CBThemedTextViewEditorFactory.themes,
            dataUpdatedEvent    : CBThemedTextViewEditorFactory.themesUpdated,
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : args.labelText,
            propertyName        : args.propertyName,
            spec                : args.spec
        });
    },

    /**
     * @return undefined
     */
    fetchThemes : function() {
        var xhr     = new XMLHttpRequest();
        xhr.onload  = CBThemedTextViewEditorFactory.fetchThemesCompleted.bind(undefined, {
            xhr : xhr
        });
        xhr.onerror = function() {
            alert("The CBThemedTextView themes failed to load.");
        };

        xhr.open("POST", "/api/?class=CBThemedTextViewEditor&function=fetchThemes");
        xhr.send();
    },

    /**
     * @param   {XMLHttpRequest}    xhr
     *
     * @return  undefined
     */
    fetchThemesCompleted : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var themes      = CBThemedTextViewEditorFactory.themes;
            themes.length   = 0;

            themes.push({ textContent : "None", value : ""});

            response.themes.forEach(function(theme) {
                themes.push(theme);
            });

            document.dispatchEvent(new Event(CBThemedTextViewEditorFactory.themesUpdated));
        } else {
            Colby.displayResponse(response);
        }
    }
};

document.addEventListener("DOMContentLoaded", function() {
    CBThemedTextViewEditorFactory.fetchThemes();
});
