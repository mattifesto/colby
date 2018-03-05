"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISelector */
/* global
    CBUI,
    CBUINavigationArrowPart,
    CBUINavigationView,
    CBUISectionItem4,
    CBUITitleAndDescriptionPart,
    Colby */

var CBUISelector = {

    /**
     * @param object args
     *
     *      {
     *          labelText: string? (deprecated)
     *          navigateToItemCallback: function?
     *          propertyName: string?
     *          spec: object?
     *          specChangedCallback: function?
     *          options: [object]
     *
     *              {
     *                  title: string
     *                  description: string?
     *                  value: mixed
     *              }
     *
     *          valueChangedCallback: function?
     *
     *              This is an alternative to the spec and specChangedCallback
     *              parameters when a spec isn't explicitly needed.
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *
     *              The element represents a CBUISectionItem4.
     *
     *          onchange: function (get, set)
     *          options: [object] (get, set)
     *          updateOptionsCallback: function (deprecated)
     *          updateValueCallback: function
     *      }
     */
    create: function (args) {
        if (args === undefined) {
            args = {};
        }

        /**
         * These default parameter values make it easier to get started with
         * this control. However, the user won't be able to change the selection
         * if the navigateToItemCallback parameter is not specified. See
         * CBUINavigationView for more information.
         */

        let title = '';
        var options = args.options || [{ title: "Default Option" }];
        var propertyName = args.propertyName || "value";
        var spec = args.spec || {};
        var onchangeCallback = args.specChangedCallback;
        var valueChangedCallback = args.valueChangedCallback;

        let sectionItem = CBUISectionItem4.create();
        let titleAndDescriptionPart = CBUITitleAndDescriptionPart.create();

        sectionItem.appendPart(titleAndDescriptionPart);
        sectionItem.appendPart(CBUINavigationArrowPart.create());

        var state = { options: undefined };

        updateOptions(options);

        let navigate = args.navigateToItemCallback;

        if (navigate === undefined) {
            if (typeof CBUINavigationView === "object" && CBUINavigationView.context) {
                navigate = CBUINavigationView.context.navigate;
            } else {
                Colby.alert("CBUISelector requires the use of CBUINavigationView");
            }
        }

        sectionItem.callback = showSelector;

        let api = {
            get element() {
                return sectionItem.element;
            },
            get onchange() {
                return onchangeCallback;
            },
            set onchange(value) {
                onchangeCallback = value;
            },
            get options() {
                return JSON.parse(JSON.stringify(state.options));
            },
            set options(value) {
                updateOptions(value);
            },
            get title() {
                return title;
            },
            set title(value) {
                title = String(value);
                titleAndDescriptionPart.title = title;
            },
            get value() {
                return spec[propertyName];
            },
            set value(value) {
                updateValue(value);
            },
            updateOptionsCallback: updateOptions, /* deprecated */
            updateValueCallback: updateValue, /* deprecated */
        };

        api.title = args.labelText || "Selection";

        return api;

        /* closure */
        function currentValueToTitle() {
            var options = state.options || [];
            var selectedOption = options.find(function (option) {
                return spec[propertyName] == option.value; /* allow string to int equality */
            });

            if (selectedOption) {
                return selectedOption.title;
            } else {
                if (spec[propertyName]) {
                    return spec[propertyName] + ' (Unknown Option)';
                } else {
                    return 'None';
                }
            }
        }

        /* closure */
        function showSelector() {
            CBUISelector.showSelector({
                callback: updateValue,
                navigateToItemCallback: navigate,
                options: state.options,
                selectValue: api.value,
                title: title,
            });
        }

        /* closure */
        function updateInterface() {
            titleAndDescriptionPart.description = currentValueToTitle();
        }

        /**
         * closure
         *
         * @param [object] options
         *
         *      {
         *          title: string
         *          description: string
         *          value: mixed
         *      }
         */
        function updateOptions(options) {
            if (Array.isArray(options)) {
                state.options = options;
            } else {
                state.options = undefined;
            }

            updateInterface();
        }

        /* closure */
        function updateValue(value) {
            spec[propertyName] = value;

            updateInterface();

            if (typeof onchangeCallback === "function") {
                onchangeCallback();
            }

            if (typeof valueChangedCallback === "function") {
                valueChangedCallback(value);
            }
        }
    },

    /**
     * @param function args.callback
     * @param [object] args.options
     *  option = { string title, string? description, mixed value }
     * @param mixed? args.selectedValue
     *
     * @return Element
     */
    createSelector: function (args) {
        var element = document.createElement("div");
        var section = CBUI.createSection();

        element.appendChild(CBUI.createHalfSpace());

        args.options.forEach(function (option) {
            let sectionItem = CBUISectionItem4.create();
            let titleAndDescriptionPart = CBUITitleAndDescriptionPart.create();
            titleAndDescriptionPart.title = option.title;
            titleAndDescriptionPart.description = option.description;

            sectionItem.callback = function () {
                args.callback(option.value);
            };

            sectionItem.appendPart(titleAndDescriptionPart);
            section.appendChild(sectionItem.element);
        });

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },

    /**
     * This function will show a selector for a list of options without having
     * to create a CBUISelector instance. It returns a promise that will have
     * the eventually selected value.
     *
     * This is useful when the user needs to select a value but the state of
     * that value does not need to be stored or displayed by the user interface.
     *
     * @param object args
     *
     *      {
     *          navigateToItemCallback: function
     *          options: [object]
     *
     *              {
     *                  title: string
     *                  description: string?
     *                  value: mixed
     *              }
     *
     *          selectedValue: mixed?
     *          title: string?
     *      }
     *
     * @return Promise -> mixed
     */
    selectValue: function (args) {
        var func = function (resolve, reject) {
            if (args.options.length === 1) {
                resolve (args.options[0].value);
                return;
            }

            CBUISelector.showSelector({
                callback: resolve,
                navigateToItemCallback: args.navigateToItemCallback,
                options: args.options,
                selectedValue: args.selectedValue,
                title: args.title,
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
    showSelector: function (args) {
        var selector = CBUISelector.createSelector({
            callback: valueSelected,
            options: args.options,
            selectedValue: args.selectedValue,
        });

        args.navigateToItemCallback.call(undefined, {
            element: selector,
            title: args.title,
        });

        function valueSelected(value) {
            args.callback(value);
            history.back();
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
    acceptValue: function (args) {
        args.updateValueCallback(args.value);
        history.back();
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     */
    createEditor: function (args) {
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
                updateValueCallback: targetUpdateValueCallback,
                value: option.value,
            }));

            section.appendChild(item);
        });

        element.appendChild(section);

        return element;
    },
};
