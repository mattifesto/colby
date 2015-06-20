"use strict";

var CBAdminPageForModels = {

    /**
     * @return {Element}
     */
    createUI : function() {
        var element         = document.createElement("div");
        element.className   = "CBAdminPageForModelsUI"
        var menu            = document.createElement("div");
        menu.className      = "menu";
        var button          = document.createElement("button");
        button.textContent  = "New";
        var select          = document.createElement("select");
        var title           = document.createElement("h1");

        button.addEventListener("click", CBAdminPageForModels.handleNewClicked.bind(undefined, {
            selectElement : select
        }));

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

        menu.appendChild(select);
        menu.appendChild(button);
        element.appendChild(menu);
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
    },

    /**
     * @param   {Element}   selectElement
     * @return  undefined
     */
    handleNewClicked : function(args) {
        var className           = CBEditableClasses[args.selectElement.value].className;
        window.location.href    = "/admin/models/edit/?className=" + className;
    }
};

(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var main    = document.getElementsByTagName("main")[0];
        var editor  = CBAdminPageForModels.createUI();
        main.appendChild(editor);
    })
})();
