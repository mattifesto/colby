"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISelector */
/* global
    CBUI,
    CBUINavigationView,
    CBUISectionItem4,
    CBUIStringsPart,
*/



var CBUISelector = {

    /**
     * @param object args
     *
     *      {
     *          labelText: string? (deprecated)
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
     *          changed: function (get, set)
     *          element: Element (readonly)
     *
     *              The element represents a CBUISectionItem4.
     *
     *          options: [object] (get, set)
     *
     *              {
     *                  title: string
     *                  description: string
     *                  value: mixed
     *              }
     *
     *          title: string (get, set)
     *          value: mixed (get, set)
     *
     *          onchange: function (get, set) (deprecated, use changed)
     *          updateOptionsCallback: function (deprecated, use options)
     *          updateValueCallback: function (deprecated, use value)
     *      }
     */
    create: function (args) {
        if (args === undefined) {
            args = {};
        }

        /**
         * These default parameter values make it easier to get started with
         * this control.
         */

        let title = '';

        let options = (
            args.options ||
            [
                {
                    title: "Default Option",
                }
            ]
        );

        var propertyName = args.propertyName || "value";
        var spec = args.spec || {};
        var onchangeCallback = args.specChangedCallback;
        var valueChangedCallback = args.valueChangedCallback;

        let element;
        let titleElement;
        let descriptionElement;

        {
            let elements = CBUI.createElementTree(
                "CBUISelector CBUI_sectionItem",
                "CBUI_container_topAndBottom CBUI_flexGrow",
                "CBUISelector_title"
            );

            element = elements[0];
            titleElement = elements[2];

            let textContainer = elements[1];

            descriptionElement = CBUI.createElement(
                "CBUISelector_description CBUI_textSize_small CBUI_textColor2"
            );

            textContainer.appendChild(
                descriptionElement
            );

            element.appendChild(
                CBUI.createElement(
                    "CBUI_navigationArrow"
                )
            );
        }

        var state = {
            options: undefined,
        };

        updateOptions(options);

        element.addEventListener(
            "click",
            function () {
                showSelector();
            }
        );

        let api = {

            /**
             * @return function|undefined
             */
            get changed() {
                return onchangeCallback;
            },

            /**
             * @param function|undefined value
             */
            set changed(value) {
                if (
                    typeof value === "function" ||
                    typeof value === "undefined"
                ) {
                    onchangeCallback = value;
                } else {
                    throw new TypeError(
                        "The changed property of a CBUISelector can only be " +
                        "set to a function or undefined."
                    );
                }
            },

            get element() {
                return element;
            },

            get onchange() {
                return onchangeCallback;
            },

            set onchange(value) {
                onchangeCallback = value;
            },

            get options() {
                return JSON.parse(
                    JSON.stringify(
                        state.options
                    )
                );
            },

            set options(value) {
                updateOptions(value);
            },

            get title() {
                return title;
            },

            set title(value) {
                title = value;
                titleElement.textContent = title;
            },

            /**
             * @return mixed
             */
            get value() {
                return spec[propertyName];
            },

            /**
             * @param mixed value
             */
            set value(value) {
                updateValue(value);
            },

            updateOptionsCallback: updateOptions, /* deprecated */
            updateValueCallback: updateValue, /* deprecated */
        };

        api.title = args.labelText || "Selection";

        return api;



        /* -- closures -- -- -- -- -- */



        /**
         * @return string
         */
        function currentValueToTitle() {
            var options = state.options || [];

            var selectedOption = options.find(
                function (option) {
                    /* allow string to int equality */
                    return spec[propertyName] == option.value;
                }
            );

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
        /* currentValueToTitle() */



        /**
         * @return undefined
         */
        function showSelector() {
            CBUISelector.showSelector(
                {
                    callback: updateValue,
                    options: state.options,
                    selectValue: api.value,
                    title: title,
                }
            );
        }
        /* showSelector() */



        /**
         * @return undefined
         */
        function updateInterface() {
            descriptionElement.textContent = currentValueToTitle();
        }



        /**
         * @param [object] options
         *
         *      {
         *          title: string
         *          description: string
         *          value: mixed
         *      }
         *
         * @return undefined
         */
        function updateOptions(
            options
        ) {
            if (Array.isArray(options)) {
                state.options = options;
            } else {
                state.options = undefined;
            }

            updateInterface();
        }
        /* updateOptions() */



        /**
         * CBUISelector.create() closure
         *
         * @param mixed value
         *
         * @return undefined
         */
        function updateValue(
            value
        ) {
            spec[propertyName] = value;

            updateInterface();

            if (typeof onchangeCallback === "function") {
                onchangeCallback();
            }

            if (typeof valueChangedCallback === "function") {
                valueChangedCallback(value);
            }
        }
        /* updateValue() */

    },
    /* create() */



    /**
     * @param object args
     *
     *      {
     *          callback: function
     *          options: [object]
     *
     *              {
     *                  title: string
     *                  description: string
     *                  value: mixed
     *              }
     *
     *          selectValue: mixed
     *      }
     *
     * @return Element
     */
    createSelector: function (
        args
    ) {
        var element = document.createElement("div");
        var section = CBUI.createSection();

        element.appendChild(
            CBUI.createHalfSpace()
        );

        args.options.forEach(
            function (option) {
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = option.title;
                stringsPart.string2 = option.description;

                stringsPart.element.classList.add('titledescription');

                sectionItem.callback = function () {
                    args.callback(option.value);
                };

                sectionItem.appendPart(stringsPart);
                section.appendChild(sectionItem.element);
            }
        );
        /* forEach */

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
    /* createSelector() */



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
        var func = function (resolve /* , reject */) {
            if (args.options.length === 1) {
                resolve (args.options[0].value);
                return;
            }

            CBUISelector.showSelector(
                {
                    callback: resolve,
                    options: args.options,
                    selectedValue: args.selectedValue,
                    title: args.title,
                }
            );
        };

        return new Promise(func);
    },
    /* selectValue() */



    /**
     * @param object $args
     *
     *      {
     *          callback: function
     *          options: [object]
     *
     *              {
     *                  title: string
     *                  description: string?
     *                  value: mixed
     *              }
     *
     *          selectedValue: mixed
     *          title: string?
     *      }
     *
     * @return undefined
     */
    showSelector: function (args) {
        var selector = CBUISelector.createSelector(
            {
                callback: valueSelected,
                options: args.options,
                selectedValue: args.selectedValue,
            }
        );

        CBUINavigationView.navigate(
            {
                element: selector,
                title: args.title,
            }
        );

        function valueSelected(value) {
            args.callback(value);
            history.back();
        }
    },
    /* showSelector() */

};
/* CBUISelector */



var CBUISelectorValueEditor = {

    /**
     * @param object args
     *
     *      {
     *          updateValueCallback: function
     *          value: mixed
     *      }
     *
     * @return undefined
     */
    acceptValue: function (args) {
        args.updateValueCallback(args.value);
        history.back();
    },



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
        var section, item;
        var targetOptions = args.spec.state.options || [];
        var targetUpdateValueCallback = args.spec.updateValueCallback;
        var element = document.createElement("div");
        element.className = "CBUISelectorValueEditor";

        section = CBUI.createSection();

        targetOptions.forEach(
            function (option) {
                item = CBUI.createSectionItem();
                var title = document.createElement("div");
                title.className = "title";
                title.textContent = option.title;
                var description = document.createElement("div");
                description.className = "description";
                description.textContent = option.description || "";

                item.appendChild(title);
                item.appendChild(description);

                item.addEventListener(
                    "click",
                    CBUISelectorValueEditor.acceptValue.bind(
                        undefined,
                        {
                            updateValueCallback: targetUpdateValueCallback,
                            value: option.value,
                        }
                    )
                );

                section.appendChild(item);
            }
        );
        /* forEach */

        element.appendChild(section);

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
/* CBUISelectorValueEditor */
