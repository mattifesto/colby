"use strict";

var CBUIRadioMenu = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return object
     */
    createMenu : function (args) {
        var changeValueCallback = CBUIRadioMenu.handleValueChanged.bind(undefined, {
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        return {
            changeValueCallback : changeValueCallback,
            currentMenuOptionUnselectedCallback : undefined,
        }
    },

    /**
     * @param object args.menu
     * @param var args.value
     *
     * @return {
     *  Element element
     * }
     */
    createMenuOption : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIRadioMenuOption";

        var menuOptionUnselectedCallback = CBUIRadioMenu.handleMenuOptionUnselected.bind(undefined, {
            element : element,
        });

        element.addEventListener("click", CBUIRadioMenu.handleMenuOptionSelected.bind(undefined, {
            element : element,
            menu : args.menu,
            menuOptionUnselectedCallback : menuOptionUnselectedCallback,
            value : args.value,
        }));

        return {
            element : element
        };
    },

    /**
     * @param Element args.element
     * @param object args.menu
     * @param function args.menuOptionUnselectedCallback
     * @param var args.value
     *
     * @return undefined
     */
    handleMenuOptionSelected : function (args) {
        if (args.menu.currentMenuOptionUnselectedCallback !== undefined) {
            args.menu.currentMenuOptionUnselectedCallback.call();
        }

        args.menu.changeValueCallback.call(args.value);
        args.element.classList.add("selected");

        args.menu.currentMenuOptionUnselectedCallback = args.menuOptionUnselectedCallback;
    },

    /**
     * @param Element args.element
     *
     * @return undefined
     */
    handleMenuOptionUnselected : function (args) {
        args.element.classList.remove("selected");
    },

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    handleValueChanged : function (args, value) {
        args.spec[args.propertyName] = value;

        args.specChangedCallback.call();
    },
};
