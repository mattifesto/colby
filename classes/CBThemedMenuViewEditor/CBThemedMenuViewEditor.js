"use strict";

var CBThemedMenuViewEditorFactory = {

    themes : [],
    themesUpdated : "CBThemedMenuViewEditorThemesUpdated",

    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var element = document.createElement("div");
        element.className = "CBThemedMenuViewEditor";
        var container = document.createElement("div");
        container.className = "container";

        // menuID

        var menuIDEditor = CBStringEditorFactory.createSelectEditor2({
            handleSpecChanged : args.specChangedCallback,
            labelTextContent : "Menu",
            propertyName : "menuID",
            spec : args.spec,
        });

        CBThemedMenuViewEditorFactory.fetchMenus({
            updateMenuIDOptionsCallback : menuIDEditor.updateSelectEditorOptionsCallback,
        });

        container.appendChild(menuIDEditor.element);

        // selectedItemName

        var selectedItemNameEditor = CBStringEditorFactory.createSelectEditor2({
            handleSpecChanged : args.specChangedCallback,
            labelTextContent : "Selected Item",
            propertyName : "selectedItemName",
            spec : args.spec,
        });

        var handleMenuIDChangedCallback = CBThemedMenuViewEditorFactory.fetchMenuItemOptions.bind(undefined, {
            menuIDEditorSelectElement : menuIDEditor.selectElement,
            updateMenuItemOptionsCallback : selectedItemNameEditor.updateSelectEditorOptionsCallback
        });

        menuIDEditor.selectElement.addEventListener("change", handleMenuIDChangedCallback);

        handleMenuIDChangedCallback.call();

        container.appendChild(selectedItemNameEditor.element);

        // themeID

        container.appendChild(CBThemedMenuViewEditorFactory.createThemeIDEditor({
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Theme",
            propertyName        : "themeID",
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
            data                : CBThemedMenuViewEditorFactory.themes,
            dataUpdatedEvent    : CBThemedMenuViewEditorFactory.themesUpdated,
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : args.labelText,
            propertyName        : args.propertyName,
            spec                : args.spec
        });
    },


    /**
     * @param Element args.menuIDEditorSelectElement
     * @param function args.updateMenuItemOptionsCallback
     *
     * @return undefined
     */
    fetchMenuItemOptions : function (args) {
        args.menuIDEditorSelectElement.disabled = true;
        var formData = new FormData();

        formData.append("menuID", args.menuIDEditorSelectElement.value);

        var xhr = new XMLHttpRequest();
        xhr.onerror = function () {
            Colby.alert("The menu item options failed to load.");
        };
        xhr.onload = CBThemedMenuViewEditorFactory.fetchMenuItemOptionsDidLoad.bind(undefined, {
            menuIDEditorSelectElement : args.menuIDEditorSelectElement,
            updateMenuItemOptionsCallback : args.updateMenuItemOptionsCallback,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBThemedMenuView&function=fetchMenuItemOptions");
        xhr.send(formData);
    },

    /**
     * @param Element args.menuIDEditorSelectElement
     * @param function args.updateMenuItemOptionsCallback
     * @param XMLHttpRequest args.xhr
     * @return undefined
     */
    fetchMenuItemOptionsDidLoad : function (args) {
        args.menuIDEditorSelectElement.disabled = false;
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.updateMenuItemOptionsCallback(response.menuItemOptions);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param function args.updateMenuIDOptionsCallback
     *
     * @return undefined
     */
    fetchMenus : function (args) {
        var xhr = new XMLHttpRequest();
        xhr.onload = CBThemedMenuViewEditorFactory.fetchMenusDidLoad.bind(undefined, {
            updateMenuIDOptionsCallback : args.updateMenuIDOptionsCallback,
            xhr : xhr,
        });
        xhr.onerror = function () {
            Colby.alert("The CBThemedMenuView menus failed to load.");
        };

        xhr.open("POST", "/api/?class=CBThemedMenuView&function=fetchMenus");
        xhr.send();
    },

    /**
     * @param function args.updateMenuIDOptionsCallback
     * @param XMLHttpRequest xhr
     *
     * @return undefined
     */
    fetchMenusDidLoad : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var menus = response.menus;

            menus.push({ textContent : "None", value : ""});
            args.updateMenuIDOptionsCallback(menus);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @return undefined
     */
    fetchThemes : function() {
        var xhr = new XMLHttpRequest();
        xhr.onload = CBThemedMenuViewEditorFactory.fetchThemesDidLoad.bind(undefined, {
            xhr : xhr
        });
        xhr.onerror = function() {
            alert("The CBThemedMenuView themes failed to load.");
        };

        xhr.open("POST", "/api/?class=CBThemedMenuView&function=fetchThemes");
        xhr.send();
    },

    /**
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    fetchThemesDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var themes = CBThemedMenuViewEditorFactory.themes;
            themes.length = 0;

            themes.push({ textContent : "None", value : ""});

            response.themes.forEach(function(theme) {
                themes.push(theme);
            });

            document.dispatchEvent(new Event(CBThemedMenuViewEditorFactory.themesUpdated));
        } else {
            Colby.displayResponse(response);
        }
    },
};

document.addEventListener("DOMContentLoaded", function() {
    CBThemedMenuViewEditorFactory.fetchThemes();
});
