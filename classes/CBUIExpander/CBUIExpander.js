"use strict";
/* jshint strict: global */
/* exported CBUIExpander */
/* global
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
     *          severity: int
     *      }
     *
     * @return object
     */
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIExpander";
        var panelElement = document.createElement("div");
        panelElement.className = "panel";
        var toggleElement = document.createElement("div");
        toggleElement.className = "toggle";
        var timeElement;

        if (args.timestamp !== undefined) {
            timeElement = Colby.unixTimestampToElement(args.timestamp);
            timeElement.classList.add("compact");
        }

        var summaryElement = document.createElement("div");
        summaryElement.className = "summary";
        summaryElement.textContent = /\s*(.*)\n?/m.exec(args.message)[1];

        var contentElement = document.createElement("div");
        contentElement.className = "content";

        var messageElement = document.createElement("div");
        messageElement.className = "message";
        messageElement.textContent = args.message;
        contentElement.appendChild(messageElement);

        toggleElement.addEventListener("click", function () {
            element.classList.toggle("expanded");
        });

        panelElement.appendChild(toggleElement);
        if (timeElement) {
            panelElement.appendChild(timeElement);
        }
        panelElement.appendChild(summaryElement);
        panelElement.appendChild(contentElement);
        element.appendChild(panelElement);

        return {
            element: element
        };
    },
};

Colby.afterDOMContentLoaded(CBUIExpander.build);
