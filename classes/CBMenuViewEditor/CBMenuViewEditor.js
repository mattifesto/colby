"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMenuViewEditor */
/* global
    CBModel,
    CBUI,
    CBUISelector,
    CBUIStringEditor2,

    CBMenuViewEditor_menuItemOptionsByMenuID,
    CBMenuViewEditor_menuOptions,
*/



var CBMenuViewEditor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



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
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

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
                    spec: spec,
                    specChangedCallback: function () {
                        updateMenuItemSelectorOptions();
                        specChangedCallback();
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
                spec: spec,
                specChangedCallback: specChangedCallback,
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

        element.appendChild(section);

        section.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "CSSClassNames",
                "CSS Class Names",
                specChangedCallback
            )
        );

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

            if (spec.menuID) {
                options = CBMenuViewEditor_menuItemOptionsByMenuID[
                    spec.menuID
                ];
            }

            menuItemSelector.updateOptionsCallback(options);
        }

    },
    /* CBUISpecEditor_createEditorElement() */



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
