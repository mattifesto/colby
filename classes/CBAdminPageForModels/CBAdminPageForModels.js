"use strict";

var CBAdminPageForModels = {

    /**
     * @return {Element}
     */
    createUI : function() {
        var element                 = document.createElement("div");
        element.className           = "CBAdminPageForModelsUI";
        var menu                    = document.createElement("div");
        menu.className              = "menu";
        var button                  = document.createElement("button");
        button.textContent          = "New";
        var exportButton            = document.createElement("button");
        exportButton.textContent    = "Export";
        var importInput             = document.createElement("input");
        importInput.type            = "file";
        importInput.style.display   = "none";
        var importButton            = document.createElement("button");
        importButton.textContent    = "Import";
        var select                  = document.createElement("select");

        // New UI

        var navigationView = CBNavigationViewFactory.createView();

        element.appendChild(navigationView.element);

        navigationView.navigate({
            element : CBModelClassListViewFactory.createElement({
                navigate : navigationView.navigate,
            }),
            title : "Editable Model Classes",
        });

        // Old UI

        button.addEventListener("click", CBAdminPageForModels.handleNewClicked.bind(undefined, {
            selectElement : select
        }));

        exportButton.addEventListener("click", CBAdminPageForModels.handleExportClicked);

        importButton.addEventListener("click", importInput.click.bind(importInput));
        importInput.addEventListener("change", CBAdminPageForModels.handleImportFileSelected.bind(undefined, {
            importButton    : importButton,
            inputElement    : importInput
        }));

        CBClassMenuItems.forEach(function(element, index) {
            var option          = document.createElement("option");
            option.textContent  = element.title;
            option.value        = index;
            select.appendChild(option);
        });

        menu.appendChild(select);
        menu.appendChild(button);
        menu.appendChild(exportButton);
        menu.appendChild(importInput);
        menu.appendChild(importButton);
        element.appendChild(menu);

        return element;
    },

    /**
     * @return undefined
     */
    handleExportClicked : function() {
        var ID = localStorage.getItem("CBSelectedModelID");

        if (ID === null) {
            return;
        }

        var URL = "/admin/models/export/?ID=" + ID;

        window.location.href = URL;
    },

    /**
     * @param   {Element}           importButton
     * @param   {XMLHttpRequest}    xhr
     *
     * @return  undefined
     */
    handleImportFileImported : function(args) {
        args.importButton.disabled  = false;
        var response                = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            document.location.reload(true);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param   {Element}   importButton
     * @param   {Element}   inputElement
     *
     * @return  undefined
     */
    handleImportFileSelected : function(args) {
        args.importButton.disabled  = true;
        var formData                = new FormData();

        formData.append("file", args.inputElement.files[0]);

        args.inputElement.value     = null;
        var xhr                     = new XMLHttpRequest();
        xhr.onload                  = CBAdminPageForModels.handleImportFileImported.bind(undefined, {
            importButton            : args.importButton,
            xhr                     : xhr
        });
        xhr.onerror                 = xhr.onload;
        xhr.open("POST", "/api/?class=CBModels&function=importModel");
        xhr.send(formData);
    },

    /**
     * @param   {Element}   selectElement
     *
     * @return  undefined
     */
    handleNewClicked : function(args) {
        var className           = CBClassMenuItems[args.selectElement.value].itemClassName;
        window.location.href    = "/admin/models/edit/?className=" + className;
    },

    /**
     * @param   {hex160}    ID
     * @param   {Element}   trElement
     *
     * @return  undefined
     */
    handleSelect : function(args) {
        var elements    = document.getElementsByClassName("CBModelRowSelected");
        var count       = elements.length;

        for (var i = 0; i < count; i++) {
            elements.item(i).classList.remove("CBModelRowSelected");
        }

        localStorage.setItem('CBSelectedModelID', args.ID);
        args.trElement.classList.add("CBModelRowSelected");

        event.stopPropagation();
    }
};

(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var main    = document.getElementsByTagName("main")[0];
        var editor  = CBAdminPageForModels.createUI();
        main.appendChild(editor);
    });
})();
