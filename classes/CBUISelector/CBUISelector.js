"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISelector */
/* global
    CBUI */

var CBUISelector = {

    /**
     * @param string? args.labelText
     * @param function? args.navigateToItemCallback
     * @param string? args.propertyName
     * @param object? args.spec
     * @param function? args.specChangedCallback
     * @param [{string title, string description, mixed value}]? args.options
     * @param function? args.valueChangedCallback
     *
     *      This is an alternative to the spec and specChangedCallback
     *      parameters when a spec isn't explicitly needed.
     *
     * @return  {
     *              element: Element
     *              updateOptionsCallback: function
     *              updateValueCallback: function
     *          }
     */
    create: function (args) {

        /**
         * These default parameter values make it easier to get started with
         * this control. However, the user won't be able to change the selection
         * if the navigateToItemCallback parameter is not specified. See
         * CBUINavigationView for more information.
         */

        var labelText = args.labelText || "Selection";
        var options = args.options || [{ title: "Default Option" }];
        var propertyName = args.propertyName || "value";
        var spec = args.spec || {};

        var element = document.createElement("div");
        element.className = "CBUISelector";
        var contentElement = document.createElement("div");
        contentElement.className = "content";
        var label = document.createElement("div");
        label.className = "label";
        label.textContent = labelText;
        var selectedValueTitle = document.createElement("div");
        var arrow = document.createElement("div");
        arrow.className = "arrow";
        arrow.textContent = ">";
        var state = { options : undefined };

        contentElement.appendChild(label);
        contentElement.appendChild(selectedValueTitle);
        element.appendChild(contentElement);
        element.appendChild(arrow);


        var updateInterfaceCallback = CBUISelector.updateInterface.bind(undefined, {
            propertyName : propertyName,
            selectedValueTitleElement : selectedValueTitle,
            spec : spec,
            state : state,
        });

        var updateOptionsCallback = CBUISelector.updateOptions.bind(undefined, {
            state : state,
            updateInterfaceCallback : updateInterfaceCallback,
        });

        var updateValueCallback = CBUISelector.updateValue.bind(undefined, {
            propertyName: propertyName,
            spec: spec,
            specChangedCallback : args.specChangedCallback,
            updateInterfaceCallback: updateInterfaceCallback,
            valueChangedCallback: args.valueChangedCallback,
        });

        updateOptionsCallback(options);

        if (args.navigateToItemCallback) {
            var clickedCallback = CBUISelector.showSelectorForControl.bind(undefined, {
                callback : updateValueCallback,
                labelText : labelText,
                navigateToItemCallback : args.navigateToItemCallback,
                propertyName : propertyName,
                spec : spec,
                state : state,
            });

            element.addEventListener("click", clickedCallback);
        }

        return {
            element : element,
            updateOptionsCallback : updateOptionsCallback,
            updateValueCallback : updateValueCallback,
        };
    },

    /**
     * @param function args.callback
     * @param object args.option
     *  { string title, string? description, mixed value }
     * @param mixed? args.selectedValue
     *
     * @return Element
     */
    createOption : function (args) {
        var element = document.createElement("div");
        element.className = "CBUISelectorOption";
        var title = document.createElement("div");
        title.className = "title";
        title.textContent = args.option.title;
        var description = document.createElement("div");
        description.className = "description";
        var nonBreakingSpace = "\u00A0";
        description.textContent = args.option.description || nonBreakingSpace;

        element.appendChild(title);
        element.appendChild(description);

        element.addEventListener("click", args.callback.bind(undefined, args.option.value));

        return element;
    },

    /**
     * @param function args.callback
     * @param [object] args.options
     *  option = { string title, string? description, mixed value }
     * @param mixed? args.selectedValue
     *
     * @return Element
     */
    createSelector : function (args) {
        var element = document.createElement("div");
        var section = CBUI.createSection();

        element.appendChild(CBUI.createHalfSpace());

        args.options.forEach(function (option) {
            var item = CBUI.createSectionItem();
            item.appendChild(CBUISelector.createOption({
                callback : args.callback,
                option : option,
                selectedValue : args.selectedValue,
            }));
            section.appendChild(item);
        });

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },

    /**
     * @param function callback
     * @param mixed value
     *
     * @return undefined
     */
    handleOptionSelected : function (callback, value) {
        callback.call(undefined, value);
        history.back();
    },

    /**
     * @param function args.navigateToItemCallback
     * @param [object] args.options
     *  object = { string title, string? description, mixed value }
     * @param mixed? args.selectedValue
     * @param string? args.title
     *
     * @return Promise -> mixed
     */
    selectValue : function (args) {
        var func = function (resolve, reject) {
            if (args.options.length === 1) {
                resolve (args.options[0].value);
                return;
            }

            CBUISelector.showSelector({
                callback : resolve,
                navigateToItemCallback : args.navigateToItemCallback,
                options : args.options,
                selectedValue : args.selectedValue,
                title : args.title,
            });
        };

        return new Promise(func);
    },

    /**
     * @param function args.callback
     * @param function args.navigateToItemCallback
     * @param [object] args.options
     *  object = { string title, string? description, mixed value }
     * @param mixed? args.selectedValue
     * @param string? args.title
     *
     * @return undefined
     */
    showSelector : function (args) {
        var optionSelectedCallback = CBUISelector.handleOptionSelected.bind(undefined, args.callback);
        var selector = CBUISelector.createSelector({
            callback : optionSelectedCallback,
            options : args.options,
            selectedValue : args.selectedValue,
        });

        args.navigateToItemCallback.call(undefined, {
            element : selector,
            title : args.title,
        });
    },

    /**
     * @param function args.callback
     * @param string args.labelText
     * @param function args.navigateToItemCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param object args.state
     *
     * @return undefined
     */
    showSelectorForControl : function (args) {
        CBUISelector.showSelector({
            callback : args.callback,
            navigateToItemCallback : args.navigateToItemCallback,
            options : args.state.options,
            selectedValue : args.spec[args.propertyName],
            title : args.labelText,
        });
    },

    /**
     * @param [object] args.options
     * @param mixed args.value
     */
    valueToTitle : function (args) {
        var options = args.options || [];
        var selectedOption = options.find(function (option) {
            return args.value == option.value; /* allow string to int equality */
        });

        if (selectedOption) {
            return selectedOption.title;
        } else {
            if (args.value) {
                return args.value + ' (Unknown Option)';
            } else {
                return 'None';
            }
        }
    },

    /**
     * @param string args.propertyName
     * @param Element args.selectedValueTitleElement
     * @param string args.spec
     * @param object args.state
     *
     * @return undefined
     */
    updateInterface : function (args) {
        args.selectedValueTitleElement.textContent = CBUISelector.valueToTitle({
            options : args.state.options,
            value : args.spec[args.propertyName],
        });
    },

    /**
     * @param object args.state
     * @param function args.updateInterfaceCallback
     * @param [object] options
     *
     * @return undefined
     */
    updateOptions : function (args, options) {
        if (Array.isArray(options)) {
            args.state.options = options;
        } else {
            args.state.options = undefined;
        }

        args.updateInterfaceCallback();
    },

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param function args.updateInterfaceCallback
     * @param function? args.valueChangedCallback
     *
     * @return undefined
     */
    updateValue: function (args, value) {
        args.spec[args.propertyName] = value;
        args.updateInterfaceCallback();

        if (typeof args.specChangedCallback === "function") {
            args.specChangedCallback();
        }

        if (typeof args.valueChangedCallback === "function") {
            args.valueChangedCallback(value);
        }
    },
};

var CBUISelectorValueEditor = {

    /**
     * @param function args.updateValueCallback
     * @param mixed args.value
     *
     * @return undefined
     */
    acceptValue : function (args) {
        args.updateValueCallback(args.value);
        history.back();
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     */
    createEditor : function (args) {
        var section, item;
        var targetOptions = args.spec.state.options || [];
        var targetUpdateValueCallback = args.spec.updateValueCallback;
        var element = document.createElement("div");
        element.className = "CBUISelectorValueEditor";

        section = CBUI.createSection();

        targetOptions.forEach(function (option) {
            item = CBUI.createSectionItem();
            var title = document.createElement("div");
            title.className = "title";
            title.textContent = option.title;
            var description = document.createElement("div");
            description.className = "description";
            description.textContent = option.description || "";

            item.appendChild(title);
            item.appendChild(description);

            item.addEventListener("click", CBUISelectorValueEditor.acceptValue.bind(undefined, {
                updateValueCallback : targetUpdateValueCallback,
                value : option.value,
            }));

            section.appendChild(item);
        });

        element.appendChild(section);

        return element;
    },
};
