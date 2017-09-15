"use strict";
/* jshint strict: global */
/* global
    Colby */

var CBUIExpander = {

    /**
     * @return undefined
     */
    build: function (args) {
        var links, message, pre;
        var elements = document.getElementsByClassName("CBUIExpander_builder");

        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            links = message = pre = undefined;

            if (!element.classList.contains("built")) {

                try {
                    message = JSON.parse(element.dataset.message);
                } catch (error) {}

                try {
                    pre = JSON.parse(element.dataset.pre);
                } catch (error) {}

                try {
                    links = JSON.parse(element.dataset.links);
                } catch (error) {}

                if (typeof message !== "string") {
                    message = "Cannot parse message";
                }

                if (typeof pre !== "string") {
                    pre = undefined;
                }

                var expander = CBUIExpander.create({
                    links: links,
                    message: message,
                    pre: pre,
                });

                element.appendChild(expander.element);

                element.classList.add("built");
            }
        }
    },

    /**
     * @param [{text: string, URI: string}] args.links
     * @param string args.message
     * @param string args.pre
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

        if (typeof args.pre === "string") {
            var preElement = document.createElement("div");
            preElement.className = "pre";
            preElement.textContent = args.pre;
            contentElement.appendChild(preElement);
        }

        if (Array.isArray(args.links)) {
            var linksElement = document.createElement("div");
            linksElement.className = "links";

            args.links.forEach(function (link) {
                var a = document.createElement("a");
                a.textContent = link.text;
                a.href = link.URI;

                linksElement.appendChild(a);
            });

            contentElement.appendChild(linksElement);
        }

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
