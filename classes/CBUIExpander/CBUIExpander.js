"use strict";
/* jshint strict: global */
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
     *          message: string
     *          severity: int?
     *          timestamp: int?
     *      }
     *
     * @return object
     */
    create: function (args) {
        var message = args.message;
        var element = document.createElement("div");
        element.className = "CBUIExpander";
        var panelElement = document.createElement("div");
        panelElement.className = "panel";
        var toggleElement = document.createElement("div");
        toggleElement.className = "toggle";
        var summaryElement = document.createElement("div");
        summaryElement.className = "summary";
        var summaryAsMarkup = /\s*(.*)\n?/m.exec(message)[1];
        var summaryAsText = CBMessageMarkup.markupToText(summaryAsMarkup);
        summaryElement.textContent = summaryAsText;
        var messageElement = document.createElement("div");
        messageElement.className = "message CBContentStyleSheet";

        if (args.severity) {
            element.classList.add("severity" + args.severity);
        }

        toggleElement.addEventListener("click", function () {
            if (!element.classList.contains("populated")) {
                messageElement.innerHTML = CBMessageMarkup.convert(message);
                element.classList.add("populated");
            }

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

        return {
            element: element
        };
    },
};

Colby.afterDOMContentLoaded(CBUIExpander.build);
