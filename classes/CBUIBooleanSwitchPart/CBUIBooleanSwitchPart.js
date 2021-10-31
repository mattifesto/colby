/* global
    CBUI,
*/



(function () {
    "use strict";

    window.CBUIBooleanSwitchPart = {
        create: CBUIBooleanSwitchPart_create,
    };



    /**
     * @return object
     *
     *      {
     *          changed: function (get, set)
     *          element: Element (readonly)
     *          CBUIBooleanSwitchPart_setIsDisabled: function
     *          value: bool (get, set)
     *      }
     */
    function
    CBUIBooleanSwitchPart_create(
    ) {
        let changed;
        let isDisabled = false;
        let value = false;


        /* element */

        let element = CBUI.createElement(
            "CBUIBooleanSwitchPart CBUI_userSelectNone"
        );


        /* slider */

        let sliderElement = CBUI.createElement("slider");

        element.appendChild(sliderElement);


        /* button */

        let buttonElement = CBUI.createElement("button");

        sliderElement.appendChild(buttonElement);

        element.addEventListener(
            "click",
            function () {
                if (
                    isDisabled === true
                ) {
                    return;
                }

                CBUIBooleanSwitchPart_setValue(
                    !value
                );
            }
        );



        /* -- functions -- */



        /**
         * @param bool newIsDisabledValue
         *
         * @return undefined
         */
        function
        CBUIBooleanSwitchPart_setIsDisabled(
            newIsDisabledValue
        ) {
            isDisabled = !!newIsDisabledValue;

            if (
                isDisabled === true
            ) {
                element.classList.add(
                    "CBUIBooleanSwitchPart_isDisabled"
                );
            } else {
                element.classList.remove(
                    "CBUIBooleanSwitchPart_isDisabled"
                );
            }
        }
        /* CBUIBooleanSwitchPart_setIsDisabled() */



        function
        CBUIBooleanSwitchPart_setValue(
            newValue
        ) {
            newValue = !!newValue;

            if (value == newValue) {
                return;
            }

            value = newValue;

            if (value === true) {
                element.classList.add("true");
            } else {
                element.classList.remove("true");
            }

            if (typeof changed === "function") {
                changed();
            }
        }
        /* CBUIBooleanSwitchPart_setValue() */



        return {
            CBUIBooleanSwitchPart_setIsDisabled,

            get changed() {
                return changed;
            },

            set changed(
                newChangedCallback
            ) {
                if (
                    typeof newChangedCallback === "function"
                ) {
                    changed = newChangedCallback;
                } else {
                    changed = undefined;
                }
            },

            get element() {
                return element;
            },

            get value() {
                return value;
            },

            set value(
                newValue
            ) {
                CBUIBooleanSwitchPart_setValue(
                    newValue
                );
            },
        };
    }
    /* CBUIBooleanSwitchPart_create() */

})();
