"use strict"; /* jshint strict: global */
/* global
    Colby */

var CBUIExpander = {

    /**
     * @param string args.links
     * @param string args.message
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
        contentElement.textContent = args.message;

        (args.links || []).forEach(function (link) {
            var a = document.createElement("a");
            a.textContent = link.text;
            a.href = link.URI;

            contentElement.appendChild(a);
        });

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
