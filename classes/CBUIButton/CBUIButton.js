(function () {
    "use strict";

    window.CBUIButton = {
        create,
    };



    /**
     * @return object
     *
     *      {
     *          disabled: bool (get, set)
     *          element: Element (get)
     *          textContent: string (get, set)
     *
     *          addClickListener()
     *          removeClickListener()
     *      }
     */
    function
    create(
    ) {
        let clickListeners = [];
        let element = document.createElement("div");
        element.className = "CBUIButton";

        let buttonElement = document.createElement("div");
        buttonElement.className = "CBUIButton_button";

        buttonElement.addEventListener("click", handleClick);

        element.appendChild(buttonElement);

        let contentElement = document.createElement("div");
        contentElement.className = "CBUIButton_content";

        buttonElement.appendChild(contentElement);

        let api = {

            CBUIButton_getElement,
            CBUIButton_setTextContent,

            CBUIButton_addClickEventListener,

            /**
             * @param function value
             *
             * @return undefined
             */
            addClickListener: CBUIButton_addClickEventListener,

            /**
             * @param function value
             *
             * @return undefined
             */
            removeClickListener: removeClickListener,

            /**
             * @return bool
             */
            get disabled() {
                return element.classList.contains("CBUIButton_disabled");
            },

            /**
             * @param bool value
             */
            set disabled(value) {
                if (value) {
                    element.classList.add("CBUIButton_disabled");
                } else {
                    element.classList.remove("CBUIButton_disabled");
                }
            },



            /**
             * @deprecated use CBUIButton_getElement()
             *
             * @return Element
             */
            get element() {
                return CBUIButton_getElement();
            },

            /**
             * @return string
             */
            get textContent() {
                return contentElement.textContent;
            },

            /**
             * @deprecated use CBUIButton_setTextContent()
             *
             * @param string value
             */
            set textContent(value) {
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
        function handleClick() {
            if (element.classList.contains("CBUIButton_disabled")) {
                return;
            }

            clickListeners.forEach(
                function (callback) {
                    callback.call();
                }
            );
        }

        /**
         * closure in create()
         *
         * @param function value
         *
         * @return undefined
         */
        function removeClickListener(value) {
            if (typeof value !== "function") {
                throw new TypeError();
            }

            clickListeners = clickListeners.filter(
                function (callback) {
                    return callback !== value;
                }
            );
        }

        return api;
    }
    /* create() */

})();
