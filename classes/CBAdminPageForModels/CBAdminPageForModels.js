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
        var models          = document.createElement("div");
        models.className    = "models";

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
            modelsElement   : models,
            selectElement   : select,
            titleElement    : title
        });

        select.addEventListener("change", handleClassChanged);

        menu.appendChild(select);
        menu.appendChild(button);
        element.appendChild(menu);
        element.appendChild(title);
        element.appendChild(models);

        handleClassChanged.call();

        return element;
    },

    /**
     * @param   {Element}   modelsElement
     * @param   {Element}   selectElement
     * @param   {Element}   titleElement
     *
     * @return  undefined
     */
    handleClassChanged : function(args) {
        args.titleElement.textContent = CBEditableClasses[args.selectElement.value].title;

        var className   = CBEditableClasses[args.selectElement.value].className;
        var formData    = new FormData();
        var xhr         = new XMLHttpRequest();
        xhr.onload      = CBAdminPageForModels.handleFetchListCompleted.bind(undefined, {
            modelsElement   : args.modelsElement,
            xhr             : xhr
        });
        xhr.onerror     = CBAdminPageForModels.handleFetchListFailed.bind(undefined, {
            modelsElement   : args.modelsElement,
            xhr             : xhr
        });

        formData.append("className",    className);
        formData.append("pageNumber",   1);
        xhr.open("POST", "/api/?class=CBAdminPageForModels&function=fetchModelList");
        xhr.send(formData);
    },

    /**
     * @param   {Element}           modelsElement
     * @param   {XMLHttpRequest}    xhr
     *
     * @return  undefined
     */
    handleFetchListCompleted : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var child;

            if (child = args.modelsElement.firstChild) {
                args.modelsElement.removeChild(child);
            }

            var table = document.createElement("table");

            response.models.forEach(function(model) {
                var tr              = document.createElement("tr");
                var edit            = document.createElement("td");
                var a               = document.createElement("a");
                a.href              = "/admin/models/edit/?ID=" + model.ID;
                a.textContent       = "edit";
                var title           = document.createElement("td");
                title.textContent   = model.title;

                edit.appendChild(a);
                tr.appendChild(edit);
                tr.appendChild(title);
                table.appendChild(tr);
            });

            args.modelsElement.appendChild(table);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param   {Element}           modelsElement
     * @param   {XMLHttpRequest}    xhr
     *
     * @return  undefined
     */
    handleFetchListFailed : function(args) {
        // TODO: implement
        alert('An error occurred while trying to retreive the model list.');
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
