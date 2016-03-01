"use strict";

var CBUISpecPropertyEditor = {

    /**
     * @param string className
     *
     * @return object
     */
    classNameToSpec : function (className) {
        if (className === undefined) {
            return undefined;
        } else {
            return { className : className };
        }
    },

    /**
     * @param string args.labelText
     * @param function args.navigateToItemCallback
     * @param [object] args.options
     *  option = { string title, string? description, mixed? value }
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return {
     *  Element element,
     * }
     */
    create : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBUISpecPropertyEditor";

        element.appendChild(CBUI.createSectionHeader({
            text : args.labelText,
        }));

        section = CBUI.createSection();

        /* spec */
        var specItem = CBUI.createSectionItem();
        section.appendChild(specItem);

        var editLayoutPreferencesCallback = CBUISpecPropertyEditor.handleEditLayoutPreferences.bind(undefined, {
            navigateToItemCallback : args.navigateToItemCallback,
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        specItem.addEventListener("click", editLayoutPreferencesCallback);

        var updateDisplayCallback = CBUISpecPropertyEditor.updateDisplay.bind(undefined, {
            propertyName : args.propertyName,
            spec : args.spec,
            specItemElement : specItem,
        });

        updateDisplayCallback();

        var updateValueCallback = CBUISpecPropertyEditor.updateValue.bind(undefined, {
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            updateDisplayCallback : updateDisplayCallback,
        });

        var selectClassCallback = CBUISpecPropertyEditor.selectClass.bind(undefined, {
            navigateToItemCallback : args.navigateToItemCallback,
            options : args.options,
            updateValueCallback : updateValueCallback,
        });

        /* change */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback : selectClassCallback,
            labelText : "Select...",
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return {
            element : element,
        };
    },

    /**
     * @param function args.navigateToItemCallback
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    handleEditLayoutPreferences : function (args) {
        var element = document.createElement("div");
        var layoutSpec = args.spec[args.propertyName];

        if (layoutSpec === undefined) {
            return;
        }

        var editor = CBUISpecEditor.create({
            navigateToItemCallback : args.navigateToItemCallback,
            spec : args.spec[args.propertyName],
            specChangedCallback : args.specChangedCallback,
        });

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(editor.element);
        element.appendChild(CBUI.createHalfSpace());

        args.navigateToItemCallback({
            element : element,
            title : args.spec.className || "Unknown",
        });
    },

    /**
     * @param function args.navigateToItemCallback
     * @param [object] args.options
     * @param function args.updateValueCallback
     *
     * @return undefined
     */
    selectClass : function (args) {
        CBUISelector.selectValue({
            navigateToItemCallback : args.navigateToItemCallback,
            options : args.options,
        })
        .then(CBUISpecPropertyEditor.classNameToSpec)
        .then(args.updateValueCallback);
    },

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param Element args.specItemElement
     *
     * @return undefined
     */
    updateDisplay : function (args) {
        var nonBreakingSpace = "\u00A0";
        var spec = args.spec[args.propertyName];
        var titleText = "None";

        if (spec && spec.className) {
            titleText = spec.className;
        }

        var element = document.createElement("div");
        element.className = "button";
        var title = document.createElement("div");
        title.className = "title";
        title.textContent = titleText;
        var description = document.createElement("div");
        description.className = "description";
        description.textContent = CBUISpec.specToDescription(args.spec.layout) || nonBreakingSpace;

        element.appendChild(title);
        element.appendChild(description);

        args.specItemElement.textContent = null;
        args.specItemElement.appendChild(element);
    },

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param function args.updateDisplayCallback
     *
     * @return undefined
     */
    updateValue : function (args, value) {
        args.spec[args.propertyName] = value;
        args.updateDisplayCallback.call();
        args.specChangedCallback.call();
    },
};
