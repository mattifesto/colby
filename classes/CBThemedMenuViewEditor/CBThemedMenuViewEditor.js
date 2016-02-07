"use strict";

var CBThemedMenuViewEditorFactory = {

    /**
     * @param function args.navigateCallback
     * @param object args.spec
     * @param function args.specChangedCallback
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
        var menuIDEditor = CBUISelector.create({
            labelText : "Menu",
            navigateCallback : args.navigateCallback,
            propertyName : "menuID",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        CBThemedMenuViewEditorFactory.fetchMenus({
            updateMenuIDOptionsCallback : menuIDEditor.updateOptionsCallback,
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
            propertyName : "menuID",
            spec : args.spec,
            updateMenuItemOptionsCallback : selectedItemNameEditor.updateSelectEditorOptionsCallback
        });

        menuIDEditor.element.addEventListener("change", handleMenuIDChangedCallback);

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
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.updateMenuItemOptionsCallback
     *
     * @return undefined
     */
    fetchMenuItemOptions : function (args) {
        var menuID = args.spec[args.propertyName];

        if (!menuID) { return; }

        //args.menuIDEditorSelectElement.disabled = true;
        var formData = new FormData();

        formData.append("menuID", menuID);

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
        //args.menuIDEditorSelectElement.disabled = false;
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

            menus.push({ title : "None", value : undefined});
            args.updateMenuIDOptionsCallback(menus);
        } else {
            Colby.displayResponse(response);
        }
    },
};
