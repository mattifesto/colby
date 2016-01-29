"use strict";

var CBUISelector = {

    /**
     * @param string args.labelText
     * @param function args.navigateCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param [{string title, string description, mixed value}] args.options
     *
     * @return {
     *  Element element,
     *  function updateOptionsCallback,
     *  function updateValueCallback,
     * }
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUISelector";
        var label = document.createElement("div");
        label.className = "label";
        label.textContent = args.labelText || "";
        var selectedValueTitle = document.createElement("div");
        var arrow = document.createElement("div");
        arrow.className = "arrow";
        arrow.textContent = ">";
        var state = {};

        element.appendChild(label);
        element.appendChild(selectedValueTitle);
        element.appendChild(arrow);

        var updateInterfaceCallback = CBUISelector.updateInterface.bind(undefined, {
            propertyName : args.propertyName,
            selectedValueTitleElement : selectedValueTitle,
            spec : args.spec,
            state : state,
        });

        var updateOptionsCallback = CBUISelector.updateOptions.bind(undefined, {
            state : state,
            updateInterfaceCallback : updateInterfaceCallback,
        });

        var updateValueCallback = CBUISelector.updateValue.bind(undefined, {
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            updateInterfaceCallback : updateInterfaceCallback,
        });

        updateOptionsCallback(args.options);

        element.addEventListener("click", args.navigateCallback.bind(undefined, {
            className : "CBUISelectorValue",
            propertyName : args.propertyName,
            spec : args.spec,
            state : state,
            updateValueCallback : updateValueCallback,
        }));

        return {
            element : element,
            updateOptionsCallback : updateOptionsCallback,
            updateValueCallback : updateValueCallback,
        };
    },

    /**
     * @param [object] args.options
     * @param mixed args.value
     */
    valueToTitle : function (args) {
        var options = args.options || [];
        options = options.filter(function (option) {
            return args.value === option.value;
        });

        if (options.length > 0) {
            return options[0].title;
        } else {
            return args.value + ' (Unknown)';
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
     *
     * @return undefined
     */
    updateValue : function (args, value) {
        args.spec[args.propertyName] = value;
        args.updateInterfaceCallback();
        args.specChangedCallback();
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
        var targetPropertyName = args.spec.propertyName;
        var targetSpec = args.spec.spec;
        var targetUpdateValueCallback = args.spec.updateValueCallback;
        var element = document.createElement("div");
        element.className = "CBUISelectorValueEditor";

        element.appendChild(CBUI.createHalfSpace());

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
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
};
