"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIStringEditor */
/* global
    CBModel,
    CBUI,
    Colby,
*/



(function () {

    /* public API */

    window.CBUIStringEditor = {
        create,
        createEditor,
        createSpecPropertyEditorElement,
    };



    /**
     * Example:
     *
     *      let nameEditor = CBUIStringEditor.create();
     *      nameEditor.title = "Name";
     *
     *      nameEditor.value = CBModel.valueToString(
     *          spec,
     *          "name"
     *      );
     *
     *      nameEditor.changed = function () {
     *          spec.name = nameEditor.value;
     *          specChangedCallback();
     *      };
     *
     *      sectionElement.appendChild(
     *          nameEditor.element
     *      );
     *
     * @param object|undefined args
     *
     *      This function is meant to be called with no parameter most of the
     *      time. This parameter is only used to specify information about the
     *      string editor that can't be changed once it has been created.
     *
     *      {
     *          inputType: string
     *
     *              "CBUIStringEditor_password"
     *
     *              "password" (deprecated)
     *
     *                  The returned editor will be a password editor.
     *
     *              "CBUIStringEditor_text"
     *
     *                  The returned editor will be a single line text editor.
     *
     *                  Using this option is not recommended. Multiline text
     *                  editors are more comfortable user interface experience
     *                  in almost all cases. If you need a single line it is
     *                  better to use a multiline editor and remove new lines
     *                  before using the input.
     *
     *                  This option exists for the specific case where you need
     *                  pressing return in a CBUIStringEditor to submit a form.
     *                  This is usually a search form.
     *      }
     *
     * @return object
     *
     *      {
     *          changed: function (get, set)
     *          element: Element (readonly)
     *
     *          focus()
     *
     *              Focus the input or textarea element.
     *
     *          name: string (get, set)
     *          title: string (get, set)
     *          value: string (get, set)
     *      }
     */
    function create(
        args
    ) {
        let changed;

        let elements = CBUI.createElementTree(
            "CBUIStringEditor",
            "CBUIStringEditor_container",
            [
                "CBUIStringEditor_label",
                "label"
            ]
        );

        let element = elements[0];
        let containerElement = elements[1];
        let label = elements[2];

        var ID = Colby.random160();
        label.htmlFor = ID;

        let input;


        /*  input type */
        {
            let inputType = CBModel.valueToString(
                args,
                "inputType"
            );

            if (
                inputType === "CBUIStringEditor_password" ||
                inputType === "password" /* deprecated */
            ) {
                input = CBUI.createElement(
                    "CBUIStringEditor_input",
                    "input"
                );

                input.type = "password";
            }

            else if (
                inputType === "CBUIStringEditor_text"
            ) {
                input = CBUI.createElement(
                    "CBUIStringEditor_input",
                    "input"
                );

                input.type = "text";
            }

            else {
                input = CBUI.createElement(
                    "CBUIStringEditor_input",
                    "textarea"
                );
            }
        }
        /*  input type */


        containerElement.appendChild(
            input
        );

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

            focus() {
                input.focus();
            },

            get name() {
                return input.name;
            },
            set name(value) {
                input.name = value;
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
    }
    /* create() */



    /**
     * @deprecated 2020_02_24
     *
     *      Use CBUISpecEditor.create().
     *
     * @param string title
     * @param object spec
     * @param string propertyName
     * @param function specChangedCallback
     * @param object|undefined args
     *
     * @return Element
     */
    function createSpecPropertyEditorElement(
        title,
        spec,
        propertyName,
        specChangedCallback,
        args
    ) {
        let editor = create(
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
    }
    /* createSpecPropertyEditorElement() */



    /**
     * @deprecated
     *
     *      Use CBUIStringEditor.create().
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
    function createEditor(
        args
    ) {
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

        var input = createInputElement(
            {
                type: args.type
            }
        );

        containerElement.appendChild(input);

        input.id = ID;
        input.placeholder = args.placeholderText || "";

        var resizeTextAreaCallback;

        if (input.tagName === "TEXTAREA") {
            resizeTextAreaCallback = function () {
                input.style.height = "0";
                input.style.height = input.scrollHeight + "px";
            };
        } else {
            resizeTextAreaCallback = function () {};
        }

        var inputCallback = handleInput.bind(
            undefined,
            {
                propertyName: args.propertyName,
                resizeTextAreaCallback: resizeTextAreaCallback,
                inputElement: input,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }
        );

        var updateLabelCallback = function (labelText) {
            label.textContent = labelText;
        };

        var updateValueCallback = updateValue.bind(
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
         * @NOTE 2015_09_24
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
    }
    /* createEditor() */



    /**
     * @param string args.type
     *
     * @return Element
     */
    function createInputElement(
        args
    ) {
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
    }
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
    function handleInput(
        args
    ) {
        args.spec[args.propertyName] = args.inputElement.value;

        args.resizeTextAreaCallback.call();
        args.specChangedCallback.call();
    }
    /* handleInput() */



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
    function updateValue(
        args,
        value
    ) {
        if (value === undefined) {
            args.inputElement.value = args.spec[args.propertyName] || "";
        } else {
            value = String(value);
            args.spec[args.propertyName] = value;
            args.inputElement.value = value;
        }

        args.specChangedCallback.call();
        args.resizeTextAreaCallback.call();
    }
    /* updateValue() */

})();
