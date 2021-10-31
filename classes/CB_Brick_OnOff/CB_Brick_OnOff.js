/* global
    CBUIBooleanSwitchPart,
*/

(function () {
    "use strict";

    window.CB_Brick_OnOff = {
        create: CB_Brick_OnOff_create,
    };



    /**
     * @return object
     *
     *      {
     *          CB_Brick_OnOff_getDescription: function
     *          CB_Brick_OnOff_setDescription: function
     *          CB_Brick_OnOff_setIsDisabled: function
     *          CB_Brick_OnOff_setIsOn: function
     *          CB_Brick_OnOff_getElement: function
     *          CB_Brick_OnOff_setTitle: function
     *      }
     */
    function
    CB_Brick_OnOff_create(
    ) {
        let changedCallback;

        let element = document.createElement(
            "div"
        );

        element.className = "CB_Brick_OnOff";

        let titleDescriptionContainerElement = document.createElement(
            "div"
        );

        titleDescriptionContainerElement.className = (
            "CB_Brick_OnOff_titleDescriptionContainer"
        );

        element.append(
            titleDescriptionContainerElement
        );

        let titleElement = document.createElement(
            "div"
        );

        titleElement.className = "CB_Brick_OnOff_title";

        titleDescriptionContainerElement.append(
            titleElement
        );

        let descriptionElement = document.createElement(
            "div"
        );

        descriptionElement.className = "CB_Brick_OnOff_description";

        titleDescriptionContainerElement.append(
            descriptionElement
        );

        let booleanSwitchPart = CBUIBooleanSwitchPart.create();

        booleanSwitchPart.changed = function () {
            if (
                changedCallback !== undefined
            ) {
                changedCallback();
            }
        };

        element.append(
            booleanSwitchPart.element
        );



        /**
         * @param function newChangedCallback
         *
         * @return undefined
         */
        function
        CB_Brick_OnOff_setChangedCallack(
            newChangedCallback
        ) {
            if (
                typeof newChangedCallback === "function"
            ) {
                changedCallback = newChangedCallback;
            } else {
                changedCallback = undefined;
            }
        }
        /* CB_Brick_OnOff_setChangedCallack() */



        /**
         * @return string
         */
        function
        CB_Brick_OnOff_getDescription(
        ) {
            return descriptionElement.textContent;
        }
        /* CB_Brick_OnOff_getDescription() */



        /**
         * @param string newDescriptionValue
         *
         * @return undefined
         */
        function
        CB_Brick_OnOff_setDescription(
            newDescriptionValue
        ) {
            descriptionElement.textContent = newDescriptionValue;
        }
        /* CB_Brick_OnOff_setDescription() */



        /**
         * @return Element
         */
        function
        CB_Brick_OnOff_getElement(
        ) {
            return element;
        }
        /* CB_Brick_OnOff_getElement() */



        /**
         * @param bool newIsDisabledValue
         *
         * @return undefined
         */
        function
        CB_Brick_OnOff_setIsDisabled(
            newIsDisabledValue
        ) {
            booleanSwitchPart.CBUIBooleanSwitchPart_setIsDisabled(
                newIsDisabledValue
            );
        }
        /* CB_Brick_OnOff_setIsDisabled() */



        /**
         * @return bool
         */
        function
        CB_Brick_OnOff_getIsOn(
        ) {
            return booleanSwitchPart.value;
        }
        /* CB_Brick_OnOff_getIsOn() */



        /**
         * @param bool newIsOnValue
         *
         * @return undefined
         */
        function
        CB_Brick_OnOff_setIsOn(
            newIsOnValue
        ) {
            booleanSwitchPart.value = !!newIsOnValue;
        }
        /* CB_Brick_OnOff_setIsOn() */



        /**
         * @param string newTitle
         *
         * @return undefined
         */
        function
        CB_Brick_OnOff_setTitle(
            newTitle
        ) {
            titleElement.textContent = newTitle;
        }
        /* CB_Brick_OnOff_setTitle() */



        return {
            CB_Brick_OnOff_setChangedCallack,
            CB_Brick_OnOff_getDescription,
            CB_Brick_OnOff_setDescription,
            CB_Brick_OnOff_setIsDisabled,
            CB_Brick_OnOff_getIsOn,
            CB_Brick_OnOff_setIsOn,
            CB_Brick_OnOff_getElement,
            CB_Brick_OnOff_setTitle,
        };
    }
    /* CB_Brick_OnOff_create() */

})();
