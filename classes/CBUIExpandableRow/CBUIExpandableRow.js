"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */

var CBUIExpandableRow = {

    /**
     * @return object
     *
     *      element Element
     *
     *      columnsElement Element
     *
     *          Add column elements to this flexbox row
     *
     *      contentElement Element
     *
     *          Add content that is hidden by default
     */
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIExpandableRow hidden";
        var columnsElement = document.createElement("div");
        columnsElement.className = "columns";
        var toggleElement = document.createElement("div");
        toggleElement.className = "toggle";
        var contentElement = document.createElement("div");
        contentElement.className = "content";

        columnsElement.addEventListener("click", function () {
            element.classList.toggle("hidden");
        });

        columnsElement.appendChild(toggleElement);
        element.appendChild(columnsElement);
        element.appendChild(contentElement);

        return {
            element,
            columnsElement,
            contentElement,
        };
    }
};
