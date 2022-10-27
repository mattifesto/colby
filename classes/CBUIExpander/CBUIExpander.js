/* global
    CBConvert,
    CBMessageMarkup,
    Colby,
*/

(function ()
{
    "use strict";



    let CBUIExpander =
    {
        build:
        CBUIExpander_build,

        create:
        CBUIExpander_create,
    };

    window.CBUIExpander =
    CBUIExpander;



    Colby.afterDOMContentLoaded(
        CBUIExpander.build
    );



    /**
     * @return undefined
     */
    function
    CBUIExpander_build(
    ) // -> undefined
    {
        let message;

        let elements =
        document.getElementsByClassName(
            "CBUIExpander_builder"
        );

        for (
            let i = 0;
            i < elements.length;
            i++
        ) {
            let element =
            elements[i];

            message =
            undefined;

            if (
                !element.classList.contains(
                    "built"
                )
            ) {
                try
                {
                    message =
                    JSON.parse(
                        element.dataset.message
                    );
                }
                catch (
                    error
                ) {
                    // do nothing
                }

                if (
                    typeof message !==
                    "string"
                ) {
                    message =
                    "Cannot parse message";
                }

                let expander =
                CBUIExpander.create(
                    {
                        message:
                        message,
                    }
                );

                element.appendChild(
                    expander.element
                );

                element.classList.add(
                    "built"
                );
            }
        }
    }
    // CBUIExpander_build()


    /**
     * Structure:
     *
     *      .CBUIExpander
     *          .CBUIExpander_container
     *              .CBUIExpander_header
     *                  .CBUIExpander_toggle
     *                  .CBUIExpander_headerTextContainer
     *                      .CBUIExpander_title
     *                      .CBUIExpander_timeContainer
     *              .CBUIExpander_contentContainer
     *                  <content element>
     *
     * @param object args
     *
     *      {
     *          message: string (message markup)
     *          severity: int?
     *          timestamp: int?
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *          message: string (get, set)
     *          severity: int (get, set)
     *          timestamp: int (get, set)
     *      }
     */
    function
    CBUIExpander_create(
        args
    ) // -> object
    {
        args =
        args ||
        {};

        let message =
        "";

        let severity;
        let timestamp;

        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CBUIExpander";

        let containerElement =
        document.createElement(
            "div"
        );

        containerElement.className =
        "CBUIExpander_container";

        rootElement.appendChild(
            containerElement
        );

        let header =
        createHeader(
            containerElement
        );

        let contentContainerElement =
        document.createElement(
            "div"
        );

        contentContainerElement.className =
        "CBUIExpander_contentContainer";

        containerElement.appendChild(
            contentContainerElement
        );

        let expanderController =
        {
            /**
             * @return Element|null
             */
            get contentElement()
            {
                return contentContainerElement.firstElementChild;
            },

            /**
             * Setting this property will change the message property to an
             * empty string.
             *
             * @param element Element
             */
            set contentElement(element) {
                contentContainerElement.textContent = "";
                message = "";

                contentContainerElement.appendChild(element);
            },

            /**
             * @return Element
             */
            get element() {
                return rootElement;
            },

            /**
             * @return bool
             */
            get expanded() {
                return rootElement.classList.contains("expanded");
            },

            /**
             * @param bool value
             */
            set expanded(value) {
                if (value) {
                    rootElement.classList.add("expanded");
                } else {
                    rootElement.classList.remove("expanded");
                }
            },

            /**
             * @return string
             */
            get message() {
                return message;
            },

            /**
             * Setting this property is a short cut for setting the
             * contentElement and title properties. It will always change the
             * contentElement property. If the title property is empty, it will
             * set the title property to first paragraph of the message when
             * converted to text.
             *
             * @param string value
             */
            set message(value) {
                value = CBConvert.valueToString(value);

                if (expanderController.title === "") {
                    let firstLineOfMessage = CBMessageMarkup.messageToText(value);
                    firstLineOfMessage = firstLineOfMessage.split("\n\n", 1)[0];

                    expanderController.title = firstLineOfMessage;
                }

                let contentElement = document.createElement("div");
                contentElement.className = "CBUIExpander_message CBContentStyleSheet";
                contentElement.innerHTML = CBMessageMarkup.messageToHTML(value);

                expanderController.contentElement = contentElement;

                message = value;
            },

            /**
             * @return int?
             */
            get severity() {
                return severity;
            },

            /**
             * @param int? value
             */
            set severity(value) {
                let newSeverity = Number.parseInt(value);

                rootElement.classList.remove("severity" + severity);

                if (Number.isNaN(newSeverity)) {
                    severity = undefined;
                } else {
                    severity = newSeverity;
                    rootElement.classList.add("severity" + severity);
                }
            },

            /**
             * @return int?
             */
            get timestamp() {
                return timestamp;
            },

            /**
             * @param int? value
             *
             *      This value should be a Unix timestamp specified in seconds,
             *      not a JavaScript timestamp specified in milliseconds.
             */
            set timestamp(value) {
                let newTimestamp = Number.parseInt(value);

                header.timeContainerElement.textContent = "";

                if (Number.isNaN(newTimestamp)) {
                    timestamp = undefined;
                } else {
                    timestamp = newTimestamp;
                    let timeElement = Colby.unixTimestampToElement(timestamp);
                    timeElement.classList.add("compact");
                    header.timeContainerElement.appendChild(timeElement);
                    Colby.updateCBTimeElementTextContent(timeElement);
                    Colby.updateTimes(true);
                }
            },

            /**
             * @return string
             */
            get title() {
                return header.titleElement.textContent || "";
            },

            /**
             * @param string value
             *
             *      The value should be plain text.
             */
            set title(value) {
                value = CBConvert.valueToString(value);
                header.titleElement.textContent = value;
            },
        };

        expanderController.message = args.message;
        expanderController.severity = args.severity;
        expanderController.timestamp = args.timestamp;

        /**
         * CBUIExpander.create() closure
         *
         * @param Element parentElementArgument
         *
         * @return object
         *
         *      {
         *          timeElement: Element
         *          titleElement: Element
         *      }
         */
        function
        createHeader(
            parentElementArgument
        ) // -> object
        {
            let headerElement =
            CBUIExpander_createHeaderElement(
                parentElementArgument
            );

            headerElement.addEventListener(
                "click",
                function (
                ) // -> undefined
                {
                    expanderController.expanded =
                    !expanderController.expanded;
                }
            );

            CBUIExpander_createSeverityIndicatorElement(
                headerElement
            );



            /* toggle */

            let toggleElement = document.createElement("div");
            toggleElement.className = "CBUIExpander_toggle";

            headerElement.appendChild(toggleElement);



            /* header container */

            let headerTextContainerElement = document.createElement("div");
            headerTextContainerElement.className = "CBUIExpander_headerTextContainer";

            headerElement.appendChild(headerTextContainerElement);

            /* title */

            let titleElement = document.createElement("div");
            titleElement.className = "CBUIExpander_title";

            headerTextContainerElement.appendChild(titleElement);

            /* time container */

            let timeContainerElement = document.createElement("div");
            timeContainerElement.className = "CBUIExpander_timeContainer";

            headerTextContainerElement.appendChild(timeContainerElement);

            return {
                timeContainerElement: timeContainerElement,
                titleElement: titleElement,
            };
        }
        // createHeader()

        return expanderController;
    }
    // CBUIExpander_create()



    /**
     * @param Element parentElementArgument
     *
     * @return Element
     */
    function
    CBUIExpander_createHeaderElement(
        parentElementArgument
    ) // -> Element
    {
        let headerElement =
        document.createElement(
            "div"
        );

        headerElement.className =
        "CBUIExpander_header";

        parentElementArgument.append(
            headerElement
        );

        return headerElement;
    }
    // CBUIExpander_createHeaderElement()



    /**
     * @param Element parentElementArgument
     *
     * @return undefined
     */
    function
    CBUIExpander_createSeverityIndicatorElement(
        parentElementArgument
    ) // -> undefined
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CBUIExpander_severityIndicator_root_element";



        let textElement =
        document.createElement(
            "div"
        );

        textElement.className =
        "CBUIExpander_severityIndicator_text_element";

        rootElement.append(
            textElement
        );



        parentElementArgument.append(
            rootElement
        );
    }
    // CBUIExpander_createSeverityIndicatorElement()

}
)();
