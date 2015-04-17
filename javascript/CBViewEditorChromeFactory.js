"use strict";

var CBViewEditorChromeFactory = {

    /**
     * @param {Element}     editorElement
     * @param {function}    handleViewDeleted
     * @param {string}      title
     *
     * @return {Element}
     */
    createChrome : function(args) {
        var element         = document.createElement("section");
        var header          = document.createElement("header");
        var h1              = document.createElement("h1");
        h1.textContent      = args.title;
        var button          = document.createElement("button");
        button.textContent  = "Delete View";

        button.addEventListener("click", args.handleViewDeleted);

        header.appendChild(h1);
        header.appendChild(button);
        element.appendChild(header);
        element.appendChild(args.editorElement);

        return element;
    }
};
