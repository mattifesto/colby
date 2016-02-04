"use strict";

var CBUIBooleanEditor = {

    /**
     * @param string args.labelText
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return {
     *  Element element,
     * }
     */
    create : function (args) {
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
