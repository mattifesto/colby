"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIExpander */
/* global
    CBMessageMarkup,
    Colby */

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
     *      }
     */
    create: function (args) {
        let message = '';
        let severity;

        var element = document.createElement("div");
        element.className = "CBUIExpander";
        var panelElement = document.createElement("div");
        panelElement.className = "panel";
        var toggleElement = document.createElement("div");
        toggleElement.className = "toggle";
        var summaryElement = document.createElement("div");
        summaryElement.className = "summary";
        var messageElement = document.createElement("div");
        messageElement.className = "message CBContentStyleSheet";

        toggleElement.addEventListener("click", function () {
            element.classList.toggle("expanded");
        });

        panelElement.appendChild(toggleElement);

        if (args.timestamp !== undefined) {
            var timeElement = Colby.unixTimestampToElement(args.timestamp);
            timeElement.classList.add("compact");
            panelElement.appendChild(timeElement);
        }

        panelElement.appendChild(summaryElement);
        panelElement.appendChild(messageElement);
        element.appendChild(panelElement);

        let o = {
            get element() {
                return element;
            },
            get message() {
                return message;
            },
            set message(value) {
                message = String(value);
                summaryElement.textContent = CBMessageMarkup.markupToText(message).split("\n\n", 1)[0];
                messageElement.innerHTML = CBMessageMarkup.convert(message);
            },
            get severity() {
                return severity;
            },
            set severity(value) {
                let newSeverity = Number.parseInt(value);

                if (Number.isNaN(newSeverity)) {
                    return;
                }

                element.classList.remove("severity" + severity);
                severity = newSeverity;
                element.classList.add("severity" + severity);
            },
        };

        o.message = args.message;
        o.severity = args.severity;

        return o;
    },
};

Colby.afterDOMContentLoaded(CBUIExpander.build);
