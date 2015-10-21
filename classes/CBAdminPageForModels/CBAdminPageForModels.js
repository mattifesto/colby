"use strict";

var CBAdminPageForModels = {

    /**
     * @param   {Object}    metadataForModel
     *
     * @return  <tr>
     */
    createModelRow : function(args) {
        var tr                  = document.createElement("tr");
        tr.className            = "CBModelRow";
        var title               = document.createElement("td");
        title.textContent       = args.metadataForModel.title;

        var handleSelect        = CBAdminPageForModels.handleSelect.bind(undefined, {
            ID          : args.metadataForModel.ID,
            trElement   : tr
        });

        tr.addEventListener("click",    handleSelect);
        tr.addEventListener("focusin",  handleSelect);

        tr.appendChild(title);

        return tr;
    },

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
        var editButton              = document.createElement("button");
        editButton.textContent      = "Edit";
        var exportButton            = document.createElement("button");
        exportButton.textContent    = "Export";
        var importInput             = document.createElement("input");
        importInput.type            = "file";
        importInput.style.display   = "none";
        var importButton            = document.createElement("button");
        importButton.textContent    = "Import";
        var select                  = document.createElement("select");
        var title                   = document.createElement("h1");
        var models                  = document.createElement("div");
        models.className            = "models";

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

        editButton.addEventListener("click", CBAdminPageForModels.handleEditClicked);

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

        var handleClassChanged = CBAdminPageForModels.handleClassChanged.bind(undefined, {
            modelsElement   : models,
            selectElement   : select,
            titleElement    : title
        });

        select.addEventListener("change", handleClassChanged);

        menu.appendChild(select);
        menu.appendChild(button);
        menu.appendChild(editButton);
        menu.appendChild(exportButton);
        menu.appendChild(importInput);
        menu.appendChild(importButton);
        element.appendChild(menu);
        element.appendChild(title);
        element.appendChild(models);

        if (CBClassMenuItems.length > 0) {
            handleClassChanged.call();
        }

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
        args.titleElement.textContent = CBClassMenuItems[args.selectElement.value].title;

        var className   = CBClassMenuItems[args.selectElement.value].itemClassName;
        var formData    = new FormData();
        var xhr         = new XMLHttpRequest();
        xhr.onload      = CBAdminPageForModels.handleFetchMetadataForModelsCompleted.bind(undefined, {
            modelsElement   : args.modelsElement,
            xhr             : xhr
        });
        xhr.onerror     = CBAdminPageForModels.handleFetchMetadataForModelsFailed.bind(undefined, {
            modelsElement   : args.modelsElement,
            xhr             : xhr
        });

        formData.append("className",    className);
        formData.append("pageNumber",   1);
        xhr.open("POST", "/api/?class=CBAdminPageForModels&function=fetchModelSummaryListForClassName");
        xhr.send(formData);
    },

    /**
     * @return undefined
     */
    handleEditClicked : function() {
        var ID = localStorage.getItem("CBSelectedModelID");

        if (ID === null) {
            return;
        }

        var URL = "/admin/models/edit/?ID=" + ID;

        window.location.href = URL;
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
     * @param   {Element}           modelsElement
     * @param   {XMLHttpRequest}    xhr
     *
     * @return  undefined
     */
    handleFetchMetadataForModelsCompleted : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var child;

            if (child = args.modelsElement.firstChild) {
                args.modelsElement.removeChild(child);
            }

            var table = document.createElement("table");

            response.modelSummaryList.forEach(function(metadataForModel) {
                table.appendChild(CBAdminPageForModels.createModelRow({
                    metadataForModel : metadataForModel
                }));
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
    handleFetchMetadataForModelsFailed : function(args) {
        // TODO: implement
        alert('An error occurred while trying to retreive the model list.');
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
    })
})();
