"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMenuViewEditor */
/* global
    CBModel,
    CBUI,
    CBUISelector,
    CBUIStringEditor,

    CBMenuViewEditor_menuItemOptionsByMenuID,
    CBMenuViewEditor_menuOptions,
*/

var CBMenuViewEditor = {

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBMenuViewEditor";

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUISelector.create(
                {
                    labelText: "Menu",
                    options: CBMenuViewEditor_menuOptions,
                    propertyName: "menuID",
                    spec: args.spec,
                    specChangedCallback: function () {
                        updateMenuItemSelectorOptions();
                        args.specChangedCallback();
                    },
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        var menuItemSelector = CBUISelector.create(
            {
                labelText: "Selected Item",
                propertyName: "selectedItemName",
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }
        );

        item.appendChild(menuItemSelector.element);
        section.appendChild(item);
        element.appendChild(section);

        /* CSSClassNames */

        element.appendChild(
            CBUI.createHalfSpace()
        );

        element.appendChild(
            CBUI.createSectionHeader(
                {
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
                }
            )
        );

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "CSS Class Names",
                    propertyName: "CSSClassNames",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(
            CBUI.createHalfSpace()
        );

        updateMenuItemSelectorOptions();

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function updateMenuItemSelectorOptions() {
            var options = [];

            if (args.spec.menuID) {
                options = CBMenuViewEditor_menuItemOptionsByMenuID[
                    args.spec.menuID
                ];
            }

            menuItemSelector.updateOptionsCallback(options);
        }
    },
    /* createEditor() */


    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let menuID = CBModel.valueAsID(spec, "menuID");

        if (menuID === undefined) {
            return undefined;
        }

        let selectedMenuOption = CBMenuViewEditor_menuOptions.find(
            function (option) {
                return option.value === menuID;
            }
        );

        if (selectedMenuOption === undefined) {
            return undefined;
        }

        if (spec.selectedItemName) {
            let description =
            selectedMenuOption.title +
            " (" +
            spec.selectedItemName +
            ")";

            return description;
        } else {
            return selectedMenuOption.title;
        }
    },
    /* CBUISpec_toDescription() */
};
/* CBMenuViewEditor */
