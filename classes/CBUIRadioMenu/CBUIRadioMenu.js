"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIRadioMenu */

/**
 * @deprecated 2018_11_23
 *
 *      Use CBUI_selectable and CBMutator
 */
var CBUIRadioMenu = {

    /**
     * @param object args
     *
     *      {
     *          propertyName: strings
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return object
     *
     *      {
     *          createOptionCallback: function
     *          setValueCallback: function
     *      }
     */
    createMenu: function (args) {
        var menuData = {
            options : [],
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        };

        var setValueCallback = CBUIRadioMenu.setValue.bind(undefined, menuData);

        var createOptionCallback = CBUIRadioMenu.createOption.bind(undefined, menuData, setValueCallback);

        return {
            createOptionCallback : createOptionCallback,
            setValueCallback : setValueCallback,
        };
    },

    /**
     * @param object menuData
     * @param function setValueCallback
     * @param object args
     *
     *      {
     *          value: mixed
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    createOption: function(menuData, setValueCallback, args) {
        var element = document.createElement("div");
        element.className = "CBUIRadioMenuOption";

        if (menuData.spec[menuData.propertyName] === args.value) {
            element.classList.add("selected");
        }

        element.addEventListener("click", setValueCallback.bind(undefined, args.value));

        var optionData = {
            element : element,
            value : args.value,
        };

        menuData.options.push(optionData);

        return {
            element : element,
        };
    },

    /**
     * @param menuData menuData
     * @param mixed value
     *
     * @return undefined
     */
    setValue: function(menuData, value) {
        menuData.spec[menuData.propertyName] = value;

        menuData.options.forEach(function (option) {
            if (option.value === value) {
                option.element.classList.add("selected");
            } else {
                option.element.classList.remove("selected");
            }
        });

        menuData.specChangedCallback.call();
    },
};
