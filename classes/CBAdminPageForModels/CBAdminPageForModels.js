"use strict";

var CBAdminPageForModels = {

    /**
     * @return {Element}
     */
    createUI : function() {
        var element         = document.createElement("div");
        element.className   = "CBAdminPageForModelsUI"
        var select          = document.createElement("select");
        var title           = document.createElement("h1");

        CBEditableClasses.forEach(function(element, index) {
            var option          = document.createElement("option");
            option.textContent  = element.title;
            option.value        = index;
            select.appendChild(option);
        });

        var handleClassChanged = CBAdminPageForModels.handleClassChanged.bind(undefined, {
            selectElement   : select,
            titleElement    : title
        });

        select.addEventListener("change", handleClassChanged);

        element.appendChild(select);
        element.appendChild(title);

        handleClassChanged.call();

        return element;
    },

    /**
     * @param   {Element}   selectElement
     * @param   {Element}   titleElement
     *
     * @return  undefined
     */
    handleClassChanged : function(args) {
        args.titleElement.textContent = CBEditableClasses[args.selectElement.value].title;
    }
};

(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var main    = document.getElementsByTagName("main")[0];
        var editor  = CBAdminPageForModels.createUI();
        main.appendChild(editor);
    })
})();
