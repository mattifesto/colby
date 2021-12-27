(function () {
    "use strict";

    window.CBUIButton = {
        create,
    };



    /**
     * @return object
     *
     *      {
     *          CBUIButton_addClickEventListener(<function>)
     *
     *          CBUIButton_getElement() -> Element
     *
     *          CBUIButton_getIsDisabled() -> bool
     *          CBUIButton_setIsDisabled(<bool>)
     *
     *          CBUIButton_setTextContent(<string>)
     *
     *          disabled: bool (get, set) (deprecated)
     *          element: Element (get) (deprecated)
     *          textContent: string (get, set) (deprecated)
     *
     *          addClickListener() (deprecated)
     *          removeClickListener() (deprecated)
     *      }
     */
    function
    create(
    ) {
        let clickListeners = [];

        let element = document.createElement(
            "div"
        );

        element.className = "CBUIButton";

        let buttonElement = document.createElement(
            "div"
        );

        buttonElement.className = "CBUIButton_button";

        buttonElement.addEventListener(
            "click",
            handleClick
        );

        element.append(
            buttonElement
        );

        let contentElement = document.createElement(
            "div"
        );

        contentElement.className = "CBUIButton_content";

        buttonElement.append(
            contentElement
        );

        let api = {

            CBUIButton_addClickEventListener,

            CBUIButton_getElement,

            CBUIButton_getIsDisabled,
            CBUIButton_setIsDisabled,

            CBUIButton_setTextContent,




            /**
             * @deprecated use CBUIButton_addClickEventListener()
             */
            addClickListener: CBUIButton_addClickEventListener,



            /**
             * @param function value
             *
             * @return undefined
             */
            removeClickListener: removeClickListener,



            /**
             * @deprecated use CBUIButton_getIsDisabled()
             */
            get disabled(
            ) {
                return CBUIButton_getIsDisabled();
            },



            /**
             * @deprecated use CBUIButton_setIsDisabled()
             */
            set disabled(
                newIsDisabledValue
            ) {
                CBUIButton_setIsDisabled(
                    newIsDisabledValue
                );
            },



            /**
             * @deprecated use CBUIButton_getElement()
             */
            get element(
            ) {
                return CBUIButton_getElement();
            },



            /**
             * @return string
             */
            get textContent(
            ) {
                return contentElement.textContent;
            },



            /**
             * @deprecated use CBUIButton_setTextContent()
             */
            set textContent(
                value
            ) {
                CBUIButton_setTextContent(
                    value
                );
            },
        };



        /**
         * @return Element
         */
        function
        CBUIButton_getElement(
        ) {
            return element;
        }
        /* CBUIButton_getElement() */



        /**
         * @return bool
         */
        function
        CBUIButton_getIsDisabled(
        ) {
            return element.classList.contains(
                "CBUIButton_disabled"
            );
        }
        /* CBUIButton_getIsDisabled() */



        /**
         * @param bool newIsDisabledValue
         */
        function
        CBUIButton_setIsDisabled(
            newIsDisabledValue
        ) {
            if (
                newIsDisabledValue
            ) {
                element.classList.add(
                    "CBUIButton_disabled"
                );
            } else {
                element.classList.remove(
                    "CBUIButton_disabled"
                );
            }
        }
        /* CBUIButton_setIsDisabled() */



        /**
         * @param string newTextContent
         *
         * @return undefined
         */
        function
        CBUIButton_setTextContent(
            newTextContent
        ) {
            contentElement.textContent = newTextContent;
        }
        /* CBUIButton_setTextContent() */



        /**
         * @param function value
         *
         * @return undefined
         */
        function
        CBUIButton_addClickEventListener(
            callback
        ) {
            if (
                typeof callback !== "function"
            ) {
                throw new TypeError();
            }

            if (
                clickListeners.includes(
                    callback
                )
            ) {
                return;
            }

            clickListeners.push(
                callback
            );
        }
        /* CBUIButton_addClickEventListener() */



        /**
         * closure in create()
         *
         * @return undefined
         */
        function
        handleClick(
        ) {
            if (
                element.classList.contains(
                    "CBUIButton_disabled"
                )
            ) {
                return;
            }

            clickListeners.forEach(
                function (
                    callback
                ) {
                    callback.call();
                }
            );
        }
        /* handleClick() */



        /**
         * closure in create()
         *
         * @param function value
         *
         * @return undefined
         */
        function
        removeClickListener(
            value
        ) {
            if (
                typeof value !== "function"
            ) {
                throw new TypeError();
            }

            clickListeners = clickListeners.filter(
                function (
                    callback
                ) {
                    return callback !== value;
                }
            );
        }

        return api;
    }
    /* create() */

})();
