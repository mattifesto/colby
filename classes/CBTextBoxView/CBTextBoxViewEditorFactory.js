"use strict";

var CBTextBoxViewEditorFactory = {

    themes          : [],
    themesUpdated   : "CBTextBoxViewEditorThemesUpdated",

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element                     = document.createElement("div");
        element.className               = "CBTextBoxViewEditor";
        var containerForSettings        = document.createElement("div");
        containerForSettings.className  = "container flex settings";
        var containerForURL             = document.createElement("div");
        containerForURL.className       = "container flex URL";
        var containerForTitle           = document.createElement("div");
        containerForTitle.className     = "container flex title";
        var containerForContent         = document.createElement("div");
        containerForContent.className   = "container content";

        containerForSettings.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : CBTextBoxViewEditorFactory.themes,
            dataUpdatedEvent    : CBTextBoxViewEditorFactory.themesUpdated,
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Theme",
            propertyName        : "themeID",
            spec                : args.spec
        }));

        containerForSettings.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Width",
            propertyName        : "width",
            spec                : args.spec
        }));

        containerForSettings.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Height",
            propertyName        : "height",
            spec                : args.spec
        }));

        containerForSettings.appendChild(CBStringEditorFactory.createSelectEditor({
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

        containerForSettings.appendChild(CBStringEditorFactory.createSelectEditor({
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

        containerForURL.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "wide",
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "URL",
            propertyName        : "URL",
            spec                : args.spec
        }));

        containerForTitle.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "wide",
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "titleAsMarkaround",
            spec                : args.spec
        }));

        containerForTitle.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Left",     value : "left" },
                { textContent : "Center",   value : "center" },
                { textContent : "Right",    value : "right" },
                { textContent : "Justify",  value : "justify" }
            ],
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Alignment",
            propertyName        : "titleAlignment",
            spec                : args.spec
        }));

        containerForTitle.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Color",
            propertyName        : "titleColor",
            spec                : args.spec
        }));

        containerForContent.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Content",
            propertyName        : "contentAsMarkaround",
            spec                : args.spec
        }));

        containerForContent.appendChild(CBStringEditorFactory.createSelectEditor({
            data                : [
                { textContent : "Left",     value : "left" },
                { textContent : "Center",   value : "center" },
                { textContent : "Right",    value : "right" },
                { textContent : "Justify",  value : "justify" }
            ],
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Alignment",
            propertyName        : "contentAlignment",
            spec                : args.spec
        }));

        containerForContent.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Color",
            propertyName        : "contentColor",
            spec                : args.spec
        }));

        element.appendChild(containerForSettings);
        element.appendChild(containerForURL);
        element.appendChild(containerForTitle);
        element.appendChild(containerForContent);

        return element;
    },

    /**
     * @return undefined
     */
    fetchThemes : function() {
        var xhr     = new XMLHttpRequest();
        xhr.onload  = CBTextBoxViewEditorFactory.fetchThemesCompleted.bind(undefined, {
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
            var themes      = CBTextBoxViewEditorFactory.themes;
            themes.length   = 0;

            themes.push({ textContent : "None", value : ""});

            response.themes.forEach(function(theme) {
                themes.push(theme);
            });

            document.dispatchEvent(new Event(CBTextBoxViewEditorFactory.themesUpdated));
        } else {
            Colby.displayResponse(response);
        }
    }
};

document.addEventListener("DOMContentLoaded", function() {
    CBTextBoxViewEditorFactory.fetchThemes();
});
