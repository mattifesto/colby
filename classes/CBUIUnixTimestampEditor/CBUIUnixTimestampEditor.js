"use strict";

var CBUIUnixTimestampEditor = {

    /**
     * @param string args.defaultValueText
     * @param string args.labelText
     * @param function args.navigateCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return {
     *  Element element,
     *  function updateLabelCallback,
     *  function updateValueCallback,
     * }
     */
    createEditor : function (args) {
        var timestamp = args.spec[args.propertyName];
        var element = document.createElement("div");
        element.className = "CBUIUnixTimestampEditor";
        var ID = Colby.random160();
        var label = document.createElement("div");
        label.className = "label";
        label.textContent = args.labelText || "";
        var value = document.createElement("div");
        value.textContent = (timestamp === undefined) ? args.defaultValueText : Colby.dateToLocaleString(new Date(timestamp * 1000));
        var arrow = document.createElement("div");
        arrow.className = "arrow";
        arrow.textContent = ">";

        element.addEventListener("click", args.navigateCallback.bind(undefined, {
            className : "CBUIUnixTimestampEditorInterface",
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }));

        element.appendChild(label);
        element.appendChild(value);
        element.appendChild(arrow);

        return {
            element : element,
        };
    },
};

var CBUIUnixTimestampEditorInterfaceEditorFactory = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param object args.state
     *
     * @return undefined
     */
    acceptValue : function(args) {
        if (args.state.date !== undefined) {
            args.spec[args.propertyName] = Math.floor(args.state.date.getTime() / 1000);
        } else {
            args.spec[args.propertyName] = undefined;
        }

        args.specChangedCallback.call();

        history.back();
    },

    /**
     * @param function args.handleSpecChanged
     * @param string args.navigateCallback
     * @param object args.spec
     *
     * @return Element
     */
    createEditor : function (args) {
        var targetPropertyName = args.spec.propertyName;
        var targetSpec = args.spec.spec;
        var targetSpecChangedCallback = args.spec.specChangedCallback;
        var element = document.createElement("div");
        var display = document.createElement("div");
        var format = document.createElement("div");
        format.textContent = "mm/dd/yyyy hh:mm pm";
        var input = document.createElement("input");
        input.type = "text";
        input.value = Colby.unixTimestampToUniversalDateString(targetSpec[targetPropertyName]);
        var state = {};

        input.addEventListener("input", CBUIUnixTimestampEditorInterfaceEditorFactory.handleInput.bind(undefined, {
            displayElement : display,
            inputElement : input,
            state : state,
        }));

        element.appendChild(format);
        element.appendChild(input);
        element.appendChild(display);

        var acceptButton = document.createElement("div");
        acceptButton.className = "button";
        acceptButton.textContent = "Accept";

        acceptButton.addEventListener("click", CBUIUnixTimestampEditorInterfaceEditorFactory.acceptValue.bind(undefined, {
            propertyName : targetPropertyName,
            spec : targetSpec,
            specChangedCallback : targetSpecChangedCallback,
            state : state,
        }));

        var clearButton = document.createElement("div");
        clearButton.className = "button";
        clearButton.textContent = "Clear";

        clearButton.addEventListener("click", CBUIUnixTimestampEditorInterfaceEditorFactory.acceptValue.bind(undefined, {
            propertyName : targetPropertyName,
            spec : targetSpec,
            specChangedCallback : targetSpecChangedCallback,
            state : { date : undefined },
        }));

        element.appendChild(acceptButton);
        element.appendChild(clearButton);

        return element;
    },

    /**
     * @param Element args.displayElement
     * @param Element args.inputElement
     * @param object args.state
     *
     * @return undefined
     */
    handleInput : function (args) {
        var timestamp = Date.parse(args.inputElement.value);

        if (Number.isNaN(timestamp)) {
            args.state.date = undefined;
            args.inputElement.style.backgroundColor = "hsl(0, 50%, 90%)";
            args.displayElement.textContent = "";
        } else {
            args.state.date = new Date(timestamp);
            args.inputElement.style.backgroundColor = "hsl(120, 50%, 90%)";
            args.displayElement.textContent = Colby.dateToLocaleString(args.state.date);
        }
    },
};
