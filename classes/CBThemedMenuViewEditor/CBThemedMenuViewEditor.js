"use strict";

var CBThemedMenuViewEditor = {

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

        var menuItemSelector = CBUISelector.create({
            labelText : "Selected Item",
            navigateCallback : args.navigateCallback,
            propertyName : "selectedItemName",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        var updateMenuItemsCallback = CBThemedMenuViewEditor.fetchMenuItems.bind(undefined, {
            spec : args.spec,
            updateOptionsCallback : menuItemSelector.updateOptionsCallback
        });

        var menuChangedCallback = CBThemedMenuViewEditor.handleMenuChanged.bind(undefined, {
            specChangedCallback : args.specChangedCallback,
            updateMenuItemsCallback : updateMenuItemsCallback,
        });

        section = CBUI.createSection();

        // menuID
        item = CBUI.createSectionItem();
        var menuSelector = CBUISelector.create({
            labelText : "Menu",
            navigateCallback : args.navigateCallback,
            propertyName : "menuID",
            spec : args.spec,
            specChangedCallback : menuChangedCallback,
        });
        CBThemedMenuViewEditor.fetchMenus({
            updateOptionsCallback : menuSelector.updateOptionsCallback,
        });
        item.appendChild(menuSelector.element);
        section.appendChild(item);

        // selectedItemName
        item = CBUI.createSectionItem();
        item.appendChild(menuItemSelector.element);
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

        updateMenuItemsCallback();

        return element;
    },

    /**
     * @param object args.spec
     * @param function args.updateOptionsCallback
     *
     * @return undefined
     */
    fetchMenuItems : function (args) {
        var menuID = args.spec.menuID;

        if (!menuID) {
            args.updateOptionsCallback([{ title : "None", value : undefined }]);
            return;
        }

        //args.menuIDEditorSelectElement.disabled = true;
        var formData = new FormData();
        formData.append("menuID", menuID);

        var xhr = new XMLHttpRequest();
        xhr.onerror = function () {
            Colby.alert("The menu item options failed to load.");
        };
        xhr.onload = CBThemedMenuViewEditor.fetchMenuItemsDidLoad.bind(undefined, {
            updateOptionsCallback : args.updateOptionsCallback,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBThemedMenuView&function=fetchMenuItemOptions");
        xhr.send(formData);
    },

    /**
     * @param function args.updateOptionsCallback
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    fetchMenuItemsDidLoad : function (args) {
        //args.menuIDEditorSelectElement.disabled = false;
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var menuItems = response.menuItemOptions;
            menuItems.unshift({ title : "None", value : undefined });
            args.updateOptionsCallback(menuItems);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param function args.updateOptionsCallback
     *
     * @return undefined
     */
    fetchMenus : function (args) {
        var xhr = new XMLHttpRequest();
        xhr.onload = CBThemedMenuViewEditor.fetchMenusDidLoad.bind(undefined, {
            updateOptionsCallback : args.updateOptionsCallback,
            xhr : xhr,
        });
        xhr.onerror = function () {
            Colby.alert("The CBThemedMenuView menus failed to load.");
        };

        xhr.open("POST", "/api/?class=CBThemedMenuView&function=fetchMenus");
        xhr.send();
    },

    /**
     * @param function args.updateOptionsCallback
     * @param XMLHttpRequest xhr
     *
     * @return undefined
     */
    fetchMenusDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var menus = response.menus;
            menus.unshift({ title : "None", value : undefined});
            args.updateOptionsCallback(menus);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param function args.specChangedCallback
     * @param function args.updateMenuItemsCallback
     *
     * @return undefined
     */
    handleMenuChanged : function (args) {
        args.specChangedCallback.call();
        args.updateMenuItemsCallback.call();
    },
};
