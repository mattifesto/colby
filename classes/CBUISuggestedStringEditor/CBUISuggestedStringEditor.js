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

        element.addEventListener("click", args.navigateCallback.bind(undefined, {
            className : "CBUIStringValue",
            labelText : args.labelText,
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }));

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

var CBUIStringValueEditor = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param object args.state
     *
     * @return undefined
     */
    acceptValue : function (args) {
        args.spec[args.propertyName] = args.state.value;

        args.specChangedCallback.call();

        history.back();
    },

    /**
     * @param args.navigateCallback
     * @param args.spec
     * @param args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var item, section;
        var targetLabelText = args.spec.labelText;
        var targetPropertyName = args.spec.propertyName;
        var targetSpec = args.spec.spec;
        var targetSpecChangedCallback = args.spec.specChangedCallback;
        var element = document.createElement("div");
        element.className = "CBUIStringValueEditor";
        var state = {
            value : targetSpec[targetPropertyName],
        };

        var editor = CBUIStringEditor.createEditor({
            labelText : targetLabelText,
            propertyName : "value",
            spec : state,
            specChangedCallback : CBUIStringValueEditor.noop,
        });

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(editor.element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        var container = document.createElement("div");
        container.className = "container";

        container.appendChild(CBUI.createButton({
            buttonClickedCallback : history.back.bind(history),
            text : "Cancel",
        }).element);

        var clearValueCallback = CBUIStringValueEditor.acceptValue.bind(undefined, {
            propertyName : targetPropertyName,
            spec : targetSpec,
            specChangedCallback : targetSpecChangedCallback,
            state : { value : undefined },
        });

        container.appendChild(CBUI.createButton({
            buttonClickedCallback : clearValueCallback,
            text : "Clear",
        }).element);

        var acceptValueCallback = CBUIStringValueEditor.acceptValue.bind(undefined, {
            propertyName : targetPropertyName,
            spec : targetSpec,
            specChangedCallback : targetSpecChangedCallback,
            state : state,
        });

        container.appendChild(CBUI.createButton({
            buttonClickedCallback : acceptValueCallback,
            text : "Accept",
        }).element);

        element.appendChild(container);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },

    noop : function () {},
};
