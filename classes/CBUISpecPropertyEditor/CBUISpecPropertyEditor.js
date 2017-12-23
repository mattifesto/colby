"use strict";
/* jshint strict: global */
/* exported CBUISpecPropertyEditor */
/* global
    CBUI,
    CBUIActionLink,
    CBUISelector,
    CBUISpec,
    CBUISpecEditor */

/**
 * This editor edits a property value that contains a spec. It allows the user
 * to change the spec to a different class. It is used by
 * CBViewPageInformationEditor to edit the layout property of a CBViewPage.
 */
var CBUISpecPropertyEditor = {

    /**
     * @param string className
     *
     * @return object
     */
    classNameToSpec: function (className) {
        if (className === undefined) {
            return undefined;
        } else {
            return { className: className };
        }
    },

    /**
     * @param object args
     *
     *      {
     *          labelText: string
     *          navigateToItemCallback: function
     *          options: [object]
     *
     *              [{
     *                  title: string
     *                  description: string?
     *                  value: mixed?
     *
     *                      In this case, the value should be a layout model
     *                      class name.
     *              }]
     *
     *          propertyName: string
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @NOTE 2017.12.23
     *
     *      This editor has awkward behavior when you select a new class name.
     *      It completely obliterates the previous spec and replaces it with a
     *      new empth sped. Maybe it can just keep the old spec but change the
     *      class name or it should at least warn the user.
     *
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    create: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBUISpecPropertyEditor";

        element.appendChild(CBUI.createSectionHeader({
            text: args.labelText,
        }));

        section = CBUI.createSection();

        /* spec */
        var specItem = CBUI.createSectionItem();
        section.appendChild(specItem);

        var editLayoutPreferencesCallback = CBUISpecPropertyEditor.handleEditLayoutPreferences.bind(undefined, {
            navigateToItemCallback: args.navigateToItemCallback,
            propertyName: args.propertyName,
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        });

        specItem.addEventListener("click", editLayoutPreferencesCallback);

        var updateDisplayCallback = CBUISpecPropertyEditor.updateDisplay.bind(undefined, {
            propertyName: args.propertyName,
            spec: args.spec,
            specItemElement: specItem,
        });

        updateDisplayCallback();

        var updateValueCallback = CBUISpecPropertyEditor.updateValue.bind(undefined, {
            propertyName: args.propertyName,
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
            updateDisplayCallback: updateDisplayCallback,
        });

        var selectClassCallback = CBUISpecPropertyEditor.selectClass.bind(undefined, {
            navigateToItemCallback: args.navigateToItemCallback,
            options: args.options,
            updateValueCallback: updateValueCallback,
        });

        /* change */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback: selectClassCallback,
            labelText: "Select...",
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return {
            element: element,
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
    handleEditLayoutPreferences: function (args) {
        var layoutSpec = args.spec[args.propertyName];

        if (layoutSpec === undefined) {
            return;
        }

        var editor = CBUISpecEditor.create({
            navigateToItemCallback: args.navigateToItemCallback,
            spec: args.spec[args.propertyName],
            specChangedCallback: args.specChangedCallback,
        });

        args.navigateToItemCallback({
            element: editor.element,
            title: args.spec.className || "Unknown",
        });
    },

    /**
     * @param function args.navigateToItemCallback
     * @param [object] args.options
     * @param function args.updateValueCallback
     *
     * @return undefined
     */
    selectClass: function (args) {
        CBUISelector.selectValue({
            navigateToItemCallback: args.navigateToItemCallback,
            options: args.options,
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
    updateDisplay: function (args) {
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
    updateValue: function (args, value) {
        args.spec[args.propertyName] = value;
        args.updateDisplayCallback.call();
        args.specChangedCallback.call();
    },
};
