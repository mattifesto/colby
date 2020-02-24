"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIStringEditor */
/* global
    CBModel,
    CBUI,
    Colby,
*/



var CBUIStringEditor = {

    /**
     * @param object|undefined args
     *
     *      This function is meant to be called with no parameter most of the
     *      time. This parameter is only used to specify information about the
     *      string editor that can't be changed once it has been created.
     *
     *      {
     *          inputType: string
     *
     *              "password" - The returned editor will be a password editor.
     *      }
     *
     * @return object
     *
     *      {
     *          changed: function (get, set)
     *          element: Element (readonly)
     *          title: string (get, set)
     *          value: string (get, set)
     *      }
     */
    create: function (
        args
    ) {
        let changed;

        let elements = CBUI.createElementTree(
            "CBUIStringEditor",
            "CBUIStringEditor_container",
            ["CBUIStringEditor_label", "label"]
        );

        let element = elements[0];
        let containerElement = elements[1];
        let label = elements[2];

        var ID = Colby.random160();
        label.htmlFor = ID;

        let input;

        {
            let inputType = CBModel.valueToString(
                args,
                "inputType"
            );

            if (inputType === "password") {
                input = CBUI.createElement(
                    "CBUIStringEditor_input",
                    "input"
                );

                input.type = "password";
            } else {
                input = CBUI.createElement(
                    "CBUIStringEditor_input",
                    "textarea"
                );
            }
        }

        containerElement.appendChild(input);

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




        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function resize() {
            input.style.height = "0";
            input.style.height = input.scrollHeight + "px";
        }
    },
    /* create() */



    /**
     * @deprecated 2020_02_24
     *
     *      This function was recently created as a replacement for
     *      createEditor(). However, upon further use, code using the
     *      CBUIStringEditor is most understandable when it uses
     *      CBUIStringEditor.create() like the following code:
     *
     *          let nameEditor = CBUIStringEditor.create();
     *          nameEditor.title = "Name";
     *
     *          nameEditor.changed = function () {
     *              userModel.name = nameEditor.value;
     *              notifyThatUserModelHasChanged();
     *          };
     *
     *          sectionElement.appendChild(
     *              nameEditor.element
     *          );
     *
     * @param string title
     * @param object spec
     * @param string propertyName
     * @param function specChangedCallback
     * @param object|undefined args
     *
     * @return Element
     */
    createSpecPropertyEditorElement(
        title,
        spec,
        propertyName,
        specChangedCallback,
        args
    ) {
        let editor = CBUIStringEditor.create(
            args
        );

        editor.title = title;
        editor.value = CBModel.valueToString(
            spec,
            propertyName
        );

        editor.changed = function () {
            spec[propertyName] = editor.value;

            specChangedCallback();
        };

        return editor.element;
    },
    /* createSpecPropertyEditorElement() */



    /**
     * @deprecated
     *
     *      Use CBUIStringEditor.create() like the code below:
     *
     *          let nameEditor = CBUIStringEditor.create();
     *          nameEditor.title = "Name";
     *
     *          nameEditor.changed = function () {
     *              userModel.name = nameEditor.value;
     *              notifyThatUserModelHasChanged();
     *          };
     *
     *          sectionElement.appendChild(
     *              nameEditor.element
     *          );
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
        let elements = CBUI.createElementTree(
            "CBUIStringEditor",
            "CBUIStringEditor_container",
            ["CBUIStringEditor_label", "label"]
        );

        let element = elements[0];
        let containerElement = elements[1];
        let label = elements[2];

        var ID = Colby.random160();
        label.htmlFor = ID;
        label.textContent = args.labelText || "";

        var input = CBUIStringEditor.createInputElement(
            {
                type: args.type
            }
        );

        containerElement.appendChild(input);

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
    /* createEditor() */



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
    /* createInputElement() */



    /**
     * @param Element args.inputElement
     * @param string args.propertyName
     * @param function args.resizeTextAreaCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    handleInput: function (args) {
        args.spec[args.propertyName] = args.inputElement.value;

        args.resizeTextAreaCallback.call();
        args.specChangedCallback.call();
    },
    /* handleInput() */



    /**
     * @return undefined
     */
    noop: function () {
    },



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
    /* updateValue() */

};
