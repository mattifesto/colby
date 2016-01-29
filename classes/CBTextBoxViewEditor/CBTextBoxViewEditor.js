"use strict";

var CBTextBoxViewEditor = {

    themes          : [],
    themesUpdated   : "CBTextBoxViewEditorThemesUpdated",

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var section, flexarea;
        var element                     = document.createElement("div");
        element.className               = "CBTextBoxViewEditor";

        section             = document.createElement("div");
        section.className   = "section";
        flexarea            = document.createElement("div");
        flexarea.className  = "flexarea";

        flexarea.appendChild(CBTextBoxViewEditor.createThemeIDEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Theme",
            propertyName        : "themeID",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "extent",
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Width",
            propertyName        : "width",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "extent",
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Height",
            propertyName        : "height",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Top",      value : "top" },
                { textContent : "Center",   value : "center" },
                { textContent : "Bottom",   value : "bottom" }
            ],
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Vertical Alignment",
            propertyName        : "verticalAlignment",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Auto",     value : "" },
                { textContent : "Start",    value : "flex-start" },
                { textContent : "End",      value : "flex-end" },
                { textContent : "Center",   value : "center" },
                { textContent : "Baseline", value : "baseline" },
                { textContent : "Stretch",  value : "stretch" }
            ],
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Align Self",
            propertyName        : "flexAlignSelf",
            spec                : args.spec
        }));

        section.appendChild(flexarea);
        element.appendChild(section);

        section             = document.createElement("div");
        section.className   = "section";
        flexarea            = document.createElement("div");
        flexarea.className  = "flexarea";

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "wide",
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "URL",
            propertyName        : "URL",
            spec                : args.spec
        }));

        section.appendChild(flexarea);
        element.appendChild(section);

        section             = document.createElement("div");
        section.className   = "section";
        flexarea            = document.createElement("div");
        flexarea.className  = "flexarea";

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "wide",
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "titleAsMarkaround",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Left",     value : "left" },
                { textContent : "Center",   value : "center" },
                { textContent : "Right",    value : "right" },
                { textContent : "Justify",  value : "justify" }
            ],
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title Text Alignment",
            propertyName        : "titleAlignment",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Color",
            propertyName        : "titleColor",
            spec                : args.spec
        }));

        section.appendChild(flexarea);
        element.appendChild(section);

        section             = document.createElement("div");
        section.className   = "section";
        flexarea            = document.createElement("div");
        flexarea.className  = "flexarea";

        section.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            propertyName        : "contentAsMarkaround",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Left",     value : "left" },
                { textContent : "Center",   value : "center" },
                { textContent : "Right",    value : "right" },
                { textContent : "Justify",  value : "justify" }
            ],
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Content Text Alignment",
            propertyName        : "contentAlignment",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Color",
            propertyName        : "contentColor",
            spec                : args.spec
        }));

        section.appendChild(flexarea);
        element.appendChild(section);

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
            data                : CBTextBoxViewEditor.themes,
            dataUpdatedEvent    : CBTextBoxViewEditor.themesUpdated,
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
        xhr.onload  = CBTextBoxViewEditor.fetchThemesCompleted.bind(undefined, {
            xhr : xhr
        });
        xhr.onerror = function() {
            alert("The CBTextBoxView themes failed to load.");
        };

        xhr.open("POST", "/api/?class=CBTextBoxView&function=fetchThemes");
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
            var themes      = CBTextBoxViewEditor.themes;
            themes.length   = 0;

            themes.push({ textContent : "None", value : ""});

            response.themes.forEach(function(theme) {
                themes.push(theme);
            });

            document.dispatchEvent(new Event(CBTextBoxViewEditor.themesUpdated));
        } else {
            Colby.displayResponse(response);
        }
    }
};

document.addEventListener("DOMContentLoaded", function() {
    CBTextBoxViewEditor.fetchThemes();
});
