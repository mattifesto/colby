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
        var section, item;
        var element = document.createElement("div");
        element.className = "CBThemedMenuViewEditor";

        section = CBUI.createSection();

        // menuID
        item = CBUI.createSectionItem();
        var menuIDEditor = CBStringEditorFactory.createSelectEditor2({
            handleSpecChanged : args.specChangedCallback,
            labelTextContent : "Menu",
            propertyName : "menuID",
            spec : args.spec,
        });

        CBThemedMenuViewEditorFactory.fetchMenus({
            updateMenuIDOptionsCallback : menuIDEditor.updateSelectEditorOptionsCallback,
        });

        item.appendChild(menuIDEditor.element);
        section.appendChild(item);

        // selectedItemName
        item = CBUI.createSectionItem();
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
        item.appendChild(selectedItemNameEditor.element);
        section.appendChild(item);


        // themeID
        item = CBUI.createSectionItem();
        item.appendChild(CBUIThemeSelector.create({
            classNameForKind : "CBMenuView",
            labelText : "Theme",
            navigateCallback : args.navigateCallback,
            propertyName : "themeID",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
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
