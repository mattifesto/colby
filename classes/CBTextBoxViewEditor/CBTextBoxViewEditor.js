"use strict";

var CBTextBoxViewEditor = {

    themes          : [],
    themesUpdated   : "CBTextBoxViewEditorThemesUpdated",

    /**
     * @param   {Object}    spec
     * @param   {function}  specChangedCallback
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var section, item, flexarea;
        var element                     = document.createElement("div");
        element.className               = "CBTextBoxViewEditor";

        section             = document.createElement("div");
        section.className   = "section";
        flexarea            = document.createElement("div");
        flexarea.className  = "flexarea";

        flexarea.appendChild(CBTextBoxViewEditor.createThemeIDEditor({
            specChangedCallback : args.specChangedCallback,
            labelText           : "Theme",
            propertyName        : "themeID",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "extent",
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Width",
            propertyName        : "width",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            className           : "extent",
            handleSpecChanged   : args.specChangedCallback,
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
            handleSpecChanged   : args.specChangedCallback,
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
            handleSpecChanged   : args.specChangedCallback,
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
            handleSpecChanged   : args.specChangedCallback,
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
            handleSpecChanged   : args.specChangedCallback,
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
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Title Text Alignment",
            propertyName        : "titleAlignment",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.specChangedCallback,
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
            handleSpecChanged   : args.specChangedCallback,
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
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Content Text Alignment",
            propertyName        : "contentAlignment",
            spec                : args.spec
        }));

        flexarea.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Color",
            propertyName        : "contentColor",
            spec                : args.spec
        }));

        section.appendChild(flexarea);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback : CBTextBoxViewEditor.copyContentsToCBThemedTextView.bind(undefined, {
                spec : args.spec,
            }),
            labelText : "Copy Contents Into a CBThemedTextView"
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        return element;
    },

    /**
     * Copies the subviews of this view into a new CBContainerView to facilitate
     * deprecating this view.
     *
     * @param [object] args.array
     *
     * @return null
     */
    copyContentsToCBThemedTextView : function (args) {
        var spec = {
            className : "CBThemedTextView",
            contentAsMarkaround : args.spec.contentAsMarkaround,
            titleAsMarkaround : args.spec.titleAsMarkaround,
            URL : args.spec.URL,
        };

        localStorage.setItem("specClipboard", JSON.stringify(spec));
    },

    /**
     * @param   {function}  specChangedCallback
     * @param   {string}    labelText
     * @param   {string}    propertyName
     * @param   {Object}    spec
     * @return  {Element}
     */
    createThemeIDEditor : function(args) {
        return CBStringEditorFactory.createSelectEditor({
            data                : CBTextBoxViewEditor.themes,
            dataUpdatedEvent    : CBTextBoxViewEditor.themesUpdated,
            handleSpecChanged   : args.specChangedCallback,
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
    },

    /**
     * @param string? spec.titleAsMarkaround
     *
     * @return string
     */
    specToDescription : function (spec) {
        return spec.titleAsMarkaround;
    },
};

document.addEventListener("DOMContentLoaded", function() {
    CBTextBoxViewEditor.fetchThemes();
});
