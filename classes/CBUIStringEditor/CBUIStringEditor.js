"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIStringEditor */
/* global
    Colby,
*/

var CBUIStringEditor = {

    /**
     * @return object
     *
     *      {
     *          changed: function (get, set)
     *          element: Element (readonly)
     *          title: string (get, set)
     *          value: string (get, set)
     *      }
     */
    create: function () {
        let changed;
        var element = document.createElement("div");
        element.className = "CBUIStringEditor";
        var ID = Colby.random160();
        var label = document.createElement("label");
        label.htmlFor = ID;
        var input = document.createElement("textarea");
        input.id = ID;

        input.addEventListener(
            "input",
            function () {
                resize();

                if (typeof changed === "function") {
                    changed();
                }
            }
        );

        element.appendChild(label);
        element.appendChild(input);

        /**
         * @NOTE 2015_09_24
         *
         *      We have two timeouts because there is a bug in Safari where the
         *      height is not calculated correctly the first time. The first
         *      height is close which is why we keep both calls. Remove the
         *      second timeout once the bug has been fixed.
         */
        window.setTimeout(resize, 0);
        window.setTimeout(resize, 1000);

        let api = {
            get changed() {
                return changed;
            },
            set changed(value) {
                changed = value;
            },
            get element() {
                return element;
            },
            get title() {
                return label.textContent;
            },
            set title(value) {
                label.textContent = value;
            },
            get value() {
                return input.value;
            },
            set value(newValue) {
                input.value = newValue;
                resize();
            },
        };

        return api;

        /* closure */
        function resize() {
            input.style.height = "0";
            input.style.height = input.scrollHeight + "px";
        }
    },

    /**
     * @deprecated use CBUIStringEditor.create()
     *
     * @param object args
     *
     *      {
     *          labelText: string
     *          placeholderText: string
     *          propertyName: string
     *          spec: object
     *          specChangedCallback: function
     *          type: string
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *          refresh: function
     *
     *              This function tells the editor that the spec has been
     *              updated and the editor's interface should now be updated to
     *              match it.
     *
     *          updateLabelCallback: function
     *          updateValueCallback: function
     *      }
     */
    createEditor: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIStringEditor";
        var ID = Colby.random160();
        var label = document.createElement("label");
        label.htmlFor = ID;
        label.textContent = args.labelText || "";
        var input = CBUIStringEditor.createInputElement({ type: args.type });
        input.id = ID;
        input.placeholder = args.placeholderText || "";
        var resizeTextAreaCallback;

        if (input.tagName === "TEXTAREA") {
            resizeTextAreaCallback = CBUIStringEditor.resizeTextArea.bind(
                undefined,
                {
                    inputElement: input,
                }
            );
        } else {
            resizeTextAreaCallback = CBUIStringEditor.noop;
        }

        var inputCallback = CBUIStringEditor.handleInput.bind(
            undefined,
            {
                propertyName: args.propertyName,
                resizeTextAreaCallback: resizeTextAreaCallback,
                inputElement: input,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }
        );

        var updateLabelCallback = CBUIStringEditor.updateLabel.bind(
            undefined,
            {
                labelElement: label,
            }
        );

        var updateValueCallback = CBUIStringEditor.updateValue.bind(
            undefined,
            {
                propertyName: args.propertyName,
                resizeTextAreaCallback: resizeTextAreaCallback,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
                inputElement: input,
            }
        );

        input.addEventListener("input", inputCallback);

        element.appendChild(label);
        element.appendChild(input);

        /**
         * @NOTE 2015.09.24
         * We have two timeouts because there is a bug in Safari where the
         * height is not calculated correctly the first time. The first height
         * is close which is why we keep both calls. Remove the second timeout
         * once the bug has been fixed.
         */
        window.setTimeout(resizeTextAreaCallback, 0);
        window.setTimeout(resizeTextAreaCallback, 1000);

        refresh();

        return {
            element: element,
            refresh: refresh,
            updateLabelCallback: updateLabelCallback,
            updateValueCallback: updateValueCallback,
        };

        function refresh() {
            input.value = args.spec[args.propertyName] || "";
        }
    },

    /**
     * @param string args.type
     *
     * @return Element
     */
    createInputElement: function (args) {
        var element;

        switch (args.type) {
            case "email":
                element = document.createElement("input");
                element.type = "email";
                break;

            case "tel":
                element = document.createElement("input");
                element.type = "tel";
                break;

            default:
                element = document.createElement("textarea");
                break;
        }

        return element;
    },

    /**
    * @param Element args.inputElement
     * @param string args.propertyName
     * @param function args.resizeTextAreaCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return  undefined
     */
    handleInput: function (args) {
        args.spec[args.propertyName] = args.inputElement.value;

        args.resizeTextAreaCallback.call();
        args.specChangedCallback.call();
    },

    /**
     * @return undefined
     */
    noop: function () { },

    /**
     * @param Element args.inputElement
     *
     * @return undefined
     */
    resizeTextArea: function (args) {
        args.inputElement.style.height = "0";
        args.inputElement.style.height = args.inputElement.scrollHeight + "px";
    },

    /**
     * @param Element args.labelElement
     * @param string labelText
     *
     * @return undefined
     */
    updateLabel: function (args, labelText) {
        args.labelElement.textContent = labelText;
    },

    /**
     * @param Element args.inputElement
     * @param string args.propertyName
     * @param function args.resizeTextAreaCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param string value
     *
     * @return undefined
     */
    updateValue: function (args, value) {
        if (value === undefined) {
            args.inputElement.value = args.spec[args.propertyName] || "";
        } else {
            value = String(value);
            args.spec[args.propertyName] = value;
            args.inputElement.value = value;
        }

        args.specChangedCallback.call();
        args.resizeTextAreaCallback.call();
    },
};
