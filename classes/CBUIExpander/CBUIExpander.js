"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIExpander */
/* global
    CBMessageMarkup,
    Colby,
*/

var CBUIExpander = {

    /**
     * @return undefined
     */
    build: function (args) {
        var message;
        var elements = document.getElementsByClassName("CBUIExpander_builder");

        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            message = undefined;

            if (!element.classList.contains("built")) {

                try {
                    message = JSON.parse(element.dataset.message);
                } catch (error) {}

                if (typeof message !== "string") {
                    message = "Cannot parse message";
                }

                var expander = CBUIExpander.create({
                    message: message,
                });

                element.appendChild(expander.element);

                element.classList.add("built");
            }
        }
    },

    /**
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
    create: function (args) {
        args = args || {};
        let message = '';
        let severity;
        let timestamp;

        var element = document.createElement("div");
        element.className = "CBUIExpander";
        let containerElement = document.createElement("div");
        containerElement.className = "container";
        var headerElement = document.createElement("div");
        headerElement.className = "header";
        var toggleElement = document.createElement("div");
        toggleElement.className = "toggle";
        let timeContainerElement = document.createElement("div");
        var summaryElement = document.createElement("div");
        summaryElement.className = "summary";
        var messageElement = document.createElement("div");
        messageElement.className = "message CBContentStyleSheet";

        toggleElement.addEventListener("click", function () {
            element.classList.toggle("expanded");
        });

        headerElement.appendChild(toggleElement);
        headerElement.appendChild(timeContainerElement);
        containerElement.appendChild(headerElement);
        containerElement.appendChild(summaryElement);
        containerElement.appendChild(messageElement);
        element.appendChild(containerElement);

        let api = {

            /**
             * @return Element
             */
            get element() {
                return element;
            },

            /**
             * @return bool
             */
            get expanded() {
                return element.classList.contains("expanded");
            },

            /**
             * @param bool value
             */
            set expanded(value) {
                if (value) {
                    element.classList.add("expanded");
                } else {
                    element.classList.remove("expanded");
                }
            },

            /**
             * @return string
             */
            get message() {
                return message;
            },

            /**
             * @param string? value
             */
            set message(value) {
                message = String(value || "");
                summaryElement.textContent = CBMessageMarkup.messageToText(message).split("\n\n", 1)[0];
                messageElement.innerHTML = CBMessageMarkup.convert(message);
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

                element.classList.remove("severity" + severity);

                if (Number.isNaN(newSeverity)) {
                    severity = undefined;
                } else {
                    severity = newSeverity;
                    element.classList.add("severity" + severity);
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

                timeContainerElement.textContent = "";

                if (Number.isNaN(newTimestamp)) {
                    timestamp = undefined;
                } else {
                    timestamp = newTimestamp;
                    let timeElement = Colby.unixTimestampToElement(timestamp);
                    timeElement.classList.add("compact");
                    timeContainerElement.appendChild(timeElement);
                    Colby.updateCBTimeElementTextContent(timeElement);
                    Colby.updateTimes(true);
                }
            },
        };

        api.message = args.message;
        api.severity = args.severity;
        api.timestamp = args.timestamp;

        return api;
    },
};

Colby.afterDOMContentLoaded(CBUIExpander.build);
