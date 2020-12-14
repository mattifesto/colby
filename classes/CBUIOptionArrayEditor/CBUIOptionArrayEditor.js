"use strict";
/* jshint strict: global */
/* exported CBUIOptionArrayEditor */

/**
 * This class is used to create many different user interface elements each of
 * which toggles a single value in an array. So if you had an array property
 * that could contain all, some, or none of the values "1", "2", and "3" you
 * would create three CBUIOptionArrayEditor elements each of which would toggle
 * one of those values presence in the array.
 *
 * For example, you could show ten products and ask the user to "Click the
 * products you are interested in." Then send the array of products in an email.
 */
var CBUIOptionArrayEditor = {

    /**
     * @param object args
     *
     *      {
     *          propertyName: string
     *          spec: object
     *          specChangedCallback: function
     *          value: mixed
     *      }
     *
     * @return object
     *
     *      {
     *          Element element,
     *          function updateValueCallback,
     *      }
     */
    CBUISpecEditor_createEditorElement: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIOptionArrayEditor";

        element.addEventListener(
            "click",
            CBUIOptionArrayEditor.handleElementClicked.bind(
                undefined,
                {
                    element: element,
                    propertyName: args.propertyName,
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                    value: args.value,
                }
            )
        );

        var array = args.spec[args.propertyName];

        if (array !== undefined && array.indexOf(args.value) >= 0) {
            element.classList.add("selected");
        }

        return {
            element: element,
        };
    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @NOTE 2017.12.23
     *
     *      This function should probably be a closure inside the createEditor
     *      function.
     *
     * @param object args
     *
     *      {
     *          element: Element
     *          propertyName: string
     *          spec: object
     *          specChangedCallback: function
     *          value: mixed
     *      }
     */
    handleElementClicked: function (args) {
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
