"use strict";

var CBArrayEditorFactory = {

    /**
     * @param [string] classNames
     * @param function handleSpecChanged
     * @param [Object] specs
     *
     * @return Element
     */
    createEditor : function (args) {
        var element = document.createElement("div");
        element.className = "CBArrayEditor";

        var section = document.createElement("div");
        section.className = "CBUISection";

        var item = document.createElement("div");
        item.className = "CBUISectionItem";
        item.textContent = "Add";

        section.appendChild(item);
        element.appendChild(section);

        return element;
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
