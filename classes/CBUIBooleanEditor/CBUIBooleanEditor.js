"use strict";
/* jshint strict: global */

/**
 * @deprecated 2018_03_09
 *
 *      Use CBUI_container_topAndBottom and CBUIBooleanSwitchPart.
 */
var CBUIBooleanEditor = {

    /**
     * @param object args
     *
     *      {
     *          labelText: string
     *          propertyName: string
     *          spec: object
     *          specChangedCallback: function
     *          valueShouldChangeCallback: function
     *
     *              This functon will be called after the user has clicked the
     *              button but before the value changes. It returns true to
     *              proceed with the change or false to cancel it.
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIBooleanEditor";
        var label = document.createElement("div");
        label.className = "label";
        label.textContent = args.labelText || "";
        var slider = document.createElement("div");
        slider.className = "slider";
        var button = document.createElement("div");
        button.className = "button";

        slider.appendChild(button);
        element.appendChild(label);
        element.appendChild(slider);

        var updateInterfaceCallback = CBUIBooleanEditor.updateInterface.bind(undefined, {
            propertyName : args.propertyName,
            sliderElement : slider,
            spec : args.spec,
        });

        slider.addEventListener("click", CBUIBooleanEditor.handleSliderClicked.bind(undefined, {
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            updateInterfaceCallback : updateInterfaceCallback,
            valueShouldChangeCallback : args.valueShouldChangeCallback,
        }));

        updateInterfaceCallback();

        return {
            element : element,
        };
    },

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param function args.updateInterfaceCallback
     * @param function? args.valueShouldChangeCallback
     *
     * @return undefined
     */
    handleSliderClicked : function (args) {
        var value = args.spec[args.propertyName];

        if (value === true) {
            value = false;
        } else {
            value = true;
        }

        if (args.valueShouldChangeCallback) {
            if (!args.valueShouldChangeCallback.call(undefined, value)) {
                return;
            }
        }

        args.spec[args.propertyName] = value;

        args.specChangedCallback();
        args.updateInterfaceCallback();
    },

    /**
     * @param string args.propertyName
     * @param Element args.sliderElement
     * @param object args.spec
     *
     * @return undefined
     */
    updateInterface : function (args) {
        var value = args.spec[args.propertyName];

        if (value === true) {
            args.sliderElement.classList.add("true");
        } else {
            args.sliderElement.classList.remove("true");
        }
    },
};
