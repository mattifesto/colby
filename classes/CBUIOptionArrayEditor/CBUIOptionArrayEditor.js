"use strict";

var CBUIOptionArrayEditor = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param var args.value
     *
     * @return {
     *  Element element,
     *  function updateValueCallback,
     * }
     */
    createEditor : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIOptionArrayEditor";

        element.addEventListener("click", CBUIOptionArrayEditor.handleElementClicked.bind(undefined, {
            element : element,
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            value : args.value,
        }));

        var array = args.spec[args.propertyName];

        if (array !== undefined && array.indexOf(args.value) >= 0) {
            element.classList.add("selected");
        }

        return {
            element : element,
        };
    },

    /**
     * @param Element args.element
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param var args.value
     */
    handleElementClicked : function (args) {
        var array = args.spec[args.propertyName];

        if (array === undefined) {
            array = args.spec[args.propertyName] = [];
        }

        var index = array.indexOf(args.value);

        if (index >= 0) {
            // This loop conservatively ensures all instances of args.value are removed.
            do {
                array.splice(index, 1);
            } while ((index = array.indexOf(args.value)) >= 0);

            args.element.classList.remove("selected");
        } else {
            array.push(args.value);
            args.element.classList.add("selected");
        }

        args.specChangedCallback.call();
    },
};
