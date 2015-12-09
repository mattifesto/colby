"use strict";

var CBUIRadioMenu = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return {
     *  function createOptionCallback,
     *  function setValueCallback
     * }
     */
    createMenu : function (args) {
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
     * @param var args.value
     *
     * @return {
     *  Element element
     * }
     */
    createOption : function(menuData, setValueCallback, args) {
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
        }
    },

    /**
     * @param menuData menuData
     * @param var value
     *
     * @return undefined
     */
    setValue : function(menuData, value) {
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
