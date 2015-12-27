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
     *  function updateLabelCallback,
     *  function updateValueCallback,
     * }
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUISelector";
        var label = document.createElement("div");
        label.className = "label";
        label.textContent = args.labelText || "";
        var value = document.createElement("div");
        value.textContent = CBUISelector.optionValueToTitle({
            options : args.options,
            value : args.spec[args.propertyName],
        });
        var arrow = document.createElement("div");
        arrow.className = "arrow";
        arrow.textContent = ">";

        element.addEventListener("click", args.navigateCallback.bind(undefined, {
            className : "CBUISelectorValue",
            options : args.options,
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

    /**
     * @param [{string title, string description, mixed value}] args.options
     * @param mixed args.value
     */
    optionValueToTitle : function (args) {
        var options = args.options.filter(function (option) {
            return args.value === option.value;
        });

        if (options.length > 0) {
            return options[0].title;
        } else {
            return args.value + ' (Unknown)';
        }
    },
};

var CBUISelectorValueEditor = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param mixed value
     *
     * @return undefined
     */
    acceptValue : function (args) {
        args.spec[args.propertyName] = args.value;

        args.specChangedCallback.call();

        history.back();
    },

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     */
    createEditor : function (args) {
        var section, item;
        var targetPropertyName = args.spec.propertyName;
        var targetSpec = args.spec.spec;
        var targetSpecChangedCallback = args.spec.specChangedCallback;
        var element = document.createElement("div");
        element.className = "CBUISelectorValueEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        args.spec.options.forEach(function (option) {
            item = CBUI.createSectionItem();
            var title = document.createElement("div");
            title.className = "title";
            title.textContent = option.title;
            var description = document.createElement("div");
            description.className = "description";
            description.textContent = option.description;

            item.appendChild(title);
            item.appendChild(description);

            item.addEventListener("click", CBUISelectorValueEditor.acceptValue.bind(undefined, {
                propertyName : targetPropertyName,
                spec : targetSpec,
                specChangedCallback : targetSpecChangedCallback,
                value : option.value,
            }));

            section.appendChild(item);
        });

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
};
