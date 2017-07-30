"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* global
    CBUI,
    CBMenuViewEditor_menuItemOptionsByMenuID,
    CBMenuViewEditor_menuOptions,
    CBUISelector,
    CBUIStringEditor */

var CBMenuViewEditor = {

    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBMenuViewEditor";

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText: "Menu",
            navigateCallback: args.navigateCallback,
            options: CBMenuViewEditor_menuOptions,
            propertyName: "menuID",
            spec: args.spec,
            specChangedCallback: function () {
                updateMenuItemSelectorOptions();
                args.specChangedCallback();
            },
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        var menuItemSelector = CBUISelector.create({
            labelText: "Selected Item",
            navigateCallback: args.navigateCallback,
            propertyName: "selectedItemName",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        });
        item.appendChild(menuItemSelector.element);
        section.appendChild(item);
        element.appendChild(section);

        /* CSSClassNames */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            paragraphs: [
                `
                View Specific CSS Class Names:
                `,`
                "submenu1": center the text.
                `,`
                "custom": disable the default view styles.
                `,`
                Supported CSS Class Names:
                `,`
                "CBLightTheme": light background and dark text.
                `,`
                "CBDarkTheme": dark background and light text.
                `
            ],
        }));

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "CSS Class Names",
            propertyName : "CSSClassNames",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        updateMenuItemSelectorOptions();

        return element;


        function updateMenuItemSelectorOptions() {
            var options = [];

            if (args.spec.menuID) {
                options = CBMenuViewEditor_menuItemOptionsByMenuID[args.spec.menuID];
            }

            menuItemSelector.updateOptionsCallback(options);
        }
    },

    /**
     * @param object spec
     *
     * @return string
     */
    specToDescription: function (spec) {
        var selectedMenuOption = CBMenuViewEditor_menuOptions.find(function (option) {
            return option.value === spec.menuID;
        });

        if (selectedMenuOption.title) {
            if (spec.selectMenuItemName) {
                return selectedMenuOption.title + " (" + spec.selectMenuItemName + ")";
            } else {
                return selectedMenuOption.title;
            }
        } else {
            return "None";
        }
    },
};
