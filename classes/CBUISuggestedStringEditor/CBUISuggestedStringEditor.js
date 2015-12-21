"use strict";

var CBUISuggestedStringEditor = {

    /**
     * @param function args.getSuggestedStringCallback
     * @param string args.labelText
     * @param function args.navigateCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return {
     *  Element element,
     *  function suggestedStringChangedCallback,
     *  function updateLabelTextCallback,
     *  function updateValueCallback,
     * }
     */
    createEditor : function (args) {
        var element = document.createElement("div");
        element.className = "CBUISuggestedStringEditor";
        var labelTextElement = document.createElement("div");
        labelTextElement.className = "label";
        var valueElement = document.createElement("div");
        var arrowElement = document.createElement("div");
        arrowElement.className = "arrow";
        arrowElement.textContent = ">";

        var displayValueCallback = CBUISuggestedStringEditor.displayValue.bind(undefined, {
            getSuggestedStringCallback : args.getSuggestedStringCallback,
            propertyName : args.propertyName,
            spec : args.spec,
            valueElement : valueElement,
        });

        displayValueCallback.call();

        var updateLabelTextCallback = CBUISuggestedStringEditor.displayLabelText.bind(undefined, {
            labelTextElement : labelTextElement,
        });

        updateLabelTextCallback.call(undefined, args.labelText);

        var updateValueCallback = CBUISuggestedStringEditor.updateValue.bind(undefined, {
            displayValueCallback : displayValueCallback,
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        element.appendChild(labelTextElement);
        element.appendChild(valueElement);
        element.appendChild(arrowElement);

        return {
            element : element,
            suggestedStringChangedCallback : displayValueCallback,
            updateLabelTextCallback : updateLabelTextCallback,
            updateValueCallback : updateValueCallback,
        };
    },

    /**
     * @param Element args.labelTextElement
     * @param string labelText
     *
     * @return undefined
     */
    displayLabelText : function (args, labelText) {
        args.labelTextElement.textContent = labelText || "";
    },

    /**
     * @param function args.getSuggestedStringCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param Element args.valueElement
     */
    displayValue : function (args) {
        var value = args.spec[args.propertyName];

        if (value === undefined) {
            args.valueElement.textContent = args.getSuggestedStringCallback.call();
        } else {
            args.valueElement.textContent = value;
        }
    },

    /**
     * @param function args.displayValueCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param string value
     *
     * @return undefined
     */
    updateValue : function (args, value) {
        args.spec[args.propertyName] = value;

        args.displayValueCallback.call();
        args.specChangedCallback.call();
    },
};
