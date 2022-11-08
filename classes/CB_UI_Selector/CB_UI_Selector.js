/* global
    CBConvert,
    CBException,
    CBModel,
    CBUI,
    CBUINavigationView,
    CBUISectionItem4,
    CBUIStringsPart,
*/


/**
 * @NOTE 2022_11_08_1667944783
 *
 *      This class was created by copying and updating CBUISelector to remove
 *      the poorly named public controller functions. The publicly exposed
 *      functions are properly name, and the file has been cleaned a lot, but it
 *      is not yet perfectly cleaned. This should be done at some later date.
 */
(function ()
{
    "use strict";



    let CB_UI_Selector =
    {
        create:
        CB_UI_Selector_create,
    };

    window.CB_UI_Selector =
    CB_UI_Selector;



    /**
     * @return object
     *
     *      {
     *          CB_UI_Selector_getArrayOfOptions,
     *          CB_UI_Selector_setArrayOfOptions,
     *          CB_UI_Selector_setChangedEventListenter,
     *          CB_UI_Selector_getElement,
     *          CB_UI_Selector_setTitle,
     *          CB_UI_Selector_getValue,
     *          CB_UI_Selector_setValue,
     *      }
     */
    function
    CB_UI_Selector_create(
    ) // -> object
    {
        let shared_arrayOfOptions;
        let shared_changedEventListener;
        let shared_currentValue;
        let shared_titleElement;
        let shared_descriptionElement;

        let shared_rootElement =
        CB_UI_Selector_createRootElement();

        /* a reference to this element isn't needed after construction */
        let shared_contentElement =
        CB_UI_Selector_createContentElement(
            shared_rootElement
        );



        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionItem",
                "CBUI_container_topAndBottom CBUI_flexGrow",
                "CB_UI_Selector_title"
            );

            let sectionItemElement =
            elements[0];

            shared_contentElement.append(
                sectionItemElement
            );

            shared_titleElement = elements[2];

            let textContainer = elements[1];

            shared_descriptionElement =
            CBUI.createElement(
                "CB_UI_Selector_description CBUI_textSize_small CBUI_textColor2"
            );

            textContainer.appendChild(
                shared_descriptionElement
            );

            sectionItemElement.appendChild(
                CBUI.createElement(
                    "CBUI_navigationArrow"
                )
            );

            sectionItemElement.addEventListener(
                "click",
                function () {
                    CB_UI_Selector_showSelector(
                        function (value) {
                            CB_UI_Selector_setValue(value);
                        },
                        shared_arrayOfOptions,
                        shared_currentValue,
                        shared_titleElement.textContent
                    );
                }
            );
        }

        CB_UI_Selector_setArrayOfOptions(
            []
        );

        let selectorController =
        {
            CB_UI_Selector_getArrayOfOptions,
            CB_UI_Selector_setArrayOfOptions,
            CB_UI_Selector_setChangedEventListenter,
            CB_UI_Selector_getElement,
            CB_UI_Selector_setTitle,
            CB_UI_Selector_getValue,
            CB_UI_Selector_setValue,
        };

        return selectorController;



        /* -- closures -- -- -- -- -- */



        /**
         * @return [object]
         */
        function
        CB_UI_Selector_getArrayOfOptions(
        ) // -> [object]
        {
            let cloneOfArrayOfOptions =
            CBModel.clone(
                shared_arrayOfOptions
            );

            return cloneOfArrayOfOptions;
        }
        // CB_UI_Selector_getArrayOfOptions()



        /**
         * @param [object] newArrayOfOptions
         *
         *      {
         *          title: string
         *          description: string
         *          value: mixed
         *      }
         *
         * @return undefined
         */
        function
        CB_UI_Selector_setArrayOfOptions(
            newArrayOfOptions
        ) // -> undefined
        {
            let newArrayOfOptionsIsValid =
            Array.isArray(
                newArrayOfOptions
            );

            if (
                newArrayOfOptionsIsValid
            ) {
                shared_arrayOfOptions =
                CBModel.clone(
                    newArrayOfOptions
                );
            }

            else
            {
                shared_arrayOfOptions =
                [];
            }

            updateInterface();
        }
        // CB_UI_Selector_setArrayOfOptions()



        /**
         * @param function newChangedEventListenerArgument
         *
         * @return undefined
         */
        function
        CB_UI_Selector_setChangedEventListenter(
            newChangedEventListenerArgument
        ) // -> undefined
        {
            if (
                typeof newChangedEventListenerArgument ===
                "function" ||
                typeof newChangedEventListenerArgument ===
                "undefined"
            ) {
                shared_changedEventListener =
                newChangedEventListenerArgument;
            }

            else
            {
                let errorMessage =
                CBConvert.stringToCleanLine(`

                    The changed event listener of a CB_UI_Selector can only be
                    set to a function or undefined.

                `);

                let error =
                new TypeError(
                    errorMessage
                );

                throw CBException.withValueRelatedError(
                    error,
                    newChangedEventListenerArgument,
                    "a05c92144ebda23d4071db1b7c208ac61d9979cf"
                );
            }
        }
        // CB_UI_Selector_setChangedEventListenter()



        /**
         * @return Element
         */
        function
        CB_UI_Selector_getElement(
        ) // -> Element
        {
            return shared_rootElement;
        }
        // CB_UI_Selector_getElement()



        /**
         * @param string newTitleArgument
         *
         * @return undefined
         */
        function
        CB_UI_Selector_setTitle(
            newTitleArgument
        ) // -> undefined
        {
            shared_titleElement.textContent =
            newTitleArgument;
        }
        // CB_UI_Selector_setTitle()



        /**
         * @return Element
         */
        function
        CB_UI_Selector_getValue(
        ) // -> Element
        {
            return shared_currentValue;
        }
        // CB_UI_Selector_getValue()



        /**
         * @param mixed newValueArgument
         *
         * @return Element
         */
        function
        CB_UI_Selector_setValue(
            newValueArgument
        ) // -> Element
        {
            shared_currentValue =
            newValueArgument;

            updateInterface();

            if (
                typeof shared_changedEventListener ===
                "function"
            ) {
                shared_changedEventListener();
            }
        }
        // CB_UI_Selector_setValue()



        /**
         * @return string
         */
        function
        currentValueToTitle(
        )
        {
            var options =
            shared_arrayOfOptions ||
            [];

            var selectedOption =
            options.find(
                function (
                    option
                )
                {
                    /* allow string to int equality */
                    return shared_currentValue == option.value;
                }
            );

            if (
                selectedOption
            ) {
                return selectedOption.title;
            }

            else
            {
                if (
                    shared_currentValue
                ) {
                    return shared_currentValue + ' (Unknown Option)';
                }

                else
                {
                    return 'None';
                }
            }
        }
        /* currentValueToTitle() */



        /**
         * @return undefined
         */
        function
        updateInterface(
        )
        {
            shared_descriptionElement.textContent =
            currentValueToTitle();
        }
        // updateInterface()

    }
    // CB_UI_Selector_create()



    /**
     * This function creates the content element for a CB_UI_Selector control.
     *
     * @param Element parentElementArgument
     *
     * @return Element
     */
    function
    CB_UI_Selector_createContentElement(
        parentElementArgument
    ) // -> Element
    {
        let contentElement =
        document.createElement(
            "div"
        );

        parentElementArgument.append(
            contentElement
        );

        contentElement.className =
        "CB_UI_Selector_content_element";

        return contentElement;
    }
    // CB_UI_Selector_createContentElement()



    /**
     * @param object args
     *
     *      {
     *          callback: function
     *          options: [object]
     *
     *              {
     *                  title: string
     *                  description: string
     *                  value: mixed
     *              }
     *
     *          selectValue: mixed
     *      }
     *
     * @return Element
     */
    function
    CB_UI_Selector_createOptionListElement(
        args
    ) {
        var element = document.createElement("div");
        var section = CBUI.createSection();

        element.appendChild(
            CBUI.createHalfSpace()
        );

        args.options.forEach(
            function (option) {
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = option.title;
                stringsPart.string2 = option.description;

                stringsPart.element.classList.add('titledescription');

                sectionItem.callback = function () {
                    args.callback(option.value);
                };

                sectionItem.appendPart(stringsPart);
                section.appendChild(sectionItem.element);
            }
        );
        /* forEach */

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    }
    // CB_UI_Selector_createOptionListElement()



    /**
     * This function creates the root element for a CB_UI_Selector control.
     *
     * @return Element
     */
    function
    CB_UI_Selector_createRootElement(
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_UI_Selector_root_element";

        return rootElement;
    }
    // CB_UI_Selector_createRootElement()



    /**
     * @param object args
     *
     *      {
     *          callback: function
     *          options: [object]
     *
     *              {
     *                  title: string
     *                  description: string?
     *                  value: mixed
     *              }
     *
     *          selectedValue: mixed
     *          title: string?
     *      }
     *
     * @return undefined
     */
    function
    CB_UI_Selector_showSelector(
        callbackArgument,
        arrayOfOptionsArgument,
        selectedValueArgument,
        titleArgument
    ) // -> undefined
    {
        let optionListElement =
        CB_UI_Selector_createOptionListElement(
            {
                callback:
                valueSelected,

                options:
                arrayOfOptionsArgument,

                selectedValue:
                selectedValueArgument,
            }
        );

        CBUINavigationView.navigate(
            {
                element:
                optionListElement,

                title:
                titleArgument,
            }
        );

        function
        valueSelected(
            value
        ) // -> undefined
        {
            callbackArgument(
                value
            );

            history.back();
        }
    }
    // CB_UI_Selector_showSelector()

})();
