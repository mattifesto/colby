"use strict";

var CBPagesImportViewController = {

    /**
     * @return void
     */
    init : function() {

        var mainElement             = document.getElementsByTagName("main")[0];
        this._button                = document.createElement("button");
        this._button.textContent    = "Import";
        this._input                 = document.createElement("input");
        this._input.type            = "file";
        this._input.style.display   = "none";

        this._button.addEventListener("click", this._input.click.bind(this._input));
        this._input.addEventListener("change", this.importArchive.bind(this));

        mainElement.appendChild(this._button);
        mainElement.appendChild(this._input);
    },

    /**
     * @return void
     */
    importArchive : function() {

        var formData = new FormData();
        formData.append("page-archive", this._input.files[0]);

        var xhr     = new XMLHttpRequest();
        xhr.onload  = this.importArchiveDidComplete.bind(this);
        xhr.open("POST", "/api/?className=CBAPIImportPageArchive");
        xhr.send(formData);

        this._xhr               = xhr;
        this._button.disabled   = true;
    },

    /**
     * @return void
     */
    importArchiveDidComplete : function() {

        var response = Colby.responseFromXMLHttpRequest(this._xhr);

        Colby.displayResponse(response);

        this._button.disabled   = false;
        this._xhr               = undefined;
    }
};

document.addEventListener("DOMContentLoaded", function() { CBPagesImportViewController.init(); });
