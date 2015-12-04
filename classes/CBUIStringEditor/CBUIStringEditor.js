"use strict";

var CBUIStringEditor = {

    /**
     * @param string args.labelText
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return {
     *  Element element
     *  function updateLabelCallback
     *  function updateValueCallback
     * }
     */
    createEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBUIStringEditor";
        var ID = Colby.random160();
        var label = document.createElement("label");
        label.htmlFor = ID;
        label.textContent = args.labelText || "";
        var textArea = document.createElement("textarea");
        textArea.id = ID;
        textArea.value = args.spec[args.propertyName] || "";

        var resizeTextAreaCallback = CBUIStringEditor.resizeTextArea.bind(undefined, {
            textAreaElement : textArea,
        });

        var inputCallback = CBStringEditor.handleInput.bind(undefined, {
            propertyName : args.propertyName,
            resizeTextAreaCallback : resizeTextAreaCallback,
            textAreaElement : textArea,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        var updateLabelCallback = CBUIStringEditor.updateLabelCallback.bind(undefined, {
            labelElement : label,
        });

        var updateValueCallback = CBUIStringEditor.updateValueCallback.bind(undefined, {
            propertyName : args.propertyName,
            resizeTextAreaCallback : resizeTextAreaCallback,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            textAreaElement : textArea,
        });

        textArea.addEventListener("input", inputCallback);

        /**
         * @NOTE 2015.09.24
         * We have two timeouts because there is a bug in Safari where the
         * height is not calculated correctly the first time. The first height
         * is close which is why we keep both calls. Remove the second timeout
         * once the bug has been fixed.
         */
        window.setTimeout(resizeTextAreaCallback, 0);
        window.setTimeout(resizeTextAreaCallback, 1000);

        return {
            element : element,
            updateLabelCallback : updateLabelCallback,
            updateValueCallback : updateValueCallback,
        };
    },

    /**
     * @param string args.propertyName
     * @param function args.resizeTextAreaCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param Element args.textAreaElement
     *
     * @return  undefined
     */
    handleInput : function(args) {
        args.spec[args.propertyName] = args.element.value;

        args.resizeTextAreaCallback.call();
        args.specChangedCallback.call();
    },

    /**
     * @param Element args.textAreaElement
     *
     * @return undefined
     */
    resizeTextArea : function(args) {
        args.textAreaElement.style.height = "0";
        args.textAreaElement.style.height = args.textAreaElement.scrollHeight + "px";
    },

    /**
     * @param Element args.labelElement
     * @param string labelText
     *
     * @return undefined
     */
    updateLabel : function (args, labelText) {
        args.labelElement.textContent = labelText;
    },

    /**
     * @param string args.propertyName
     * @param function args.resizeTextAreaCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param Element args.textAreaElement
     * @param string value
     *
     * @return undefined
     */
    updateValue : function (args, value) {
        if (value === undefined) {
            args.textAreaElement.value = args.spec[args.propertyName] || "";
        } else {
            value = String(value);
            args.spec[args.propertyName] = value;
            args.textAreaElement.value = value;
        }

        args.resizeTextAreaCallback.call();
    },
};
