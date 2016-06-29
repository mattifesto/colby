"use strict"; /* jshint strict: global */
/* globals CBUI, CBUIActionLink, Colby */

var CBAdminPageForModelImport = {

    /**
     * @return undefined
     */
    DOMContentDidLoad : function() {
        var main = document.getElementsByTagName("main")[0];
        var element = document.createElement("div");
        element.className = "CBAdminPageForModelImport";

        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";

        var actionLink = CBUIActionLink.create({
            "callback" : input.click.bind(input),
            "labelText" : "Upload CSV Files...",
        });

        input.addEventListener("change", CBAdminPageForModelImport.handleFileInputChanged.bind(undefined, {
            disableActionLinkCallback : actionLink.disableCallback,
            enableActionLinkCallback : actionLink.enableCallback,
            fileInputElement : input,
        }));

        element.appendChild(input);

        element.appendChild(CBUI.createHalfSpace());

        var section = CBUI.createSection();
        var item = CBUI.createSectionItem();
        item.appendChild(actionLink.element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        main.appendChild(element);
    },

    /**
     * @param function args.enableActionLinkCallback
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleDataFileUploadRequestDidError : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        Colby.displayResponse(response);

        args.enableActionLinkCallback.call();
    },

    /**
     * @param function args.enableActionLinkCallback
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleDataFileUploadRequestDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        Colby.displayResponse(response);

        args.enableActionLinkCallback.call();
    },

    /**
     * @param function args.disableActionLinkCallback
     * @param function args.enableActionLinkCallback
     * @param Element args.fileInputElement
     *
     * @return undefined
     */
    handleFileInputChanged : function(args) {
        args.disableActionLinkCallback.call();

        var formData = new FormData();
        formData.append("dataFile", args.fileInputElement.files[0]);

        args.fileInputElement.value = null;

        var xhr = new XMLHttpRequest();
        xhr.onerror = CBAdminPageForModelImport.handleDataFileUploadRequestDidError.bind(undefined, {
            enableActionLinkCallback : args.enableActionLinkCallback,
            xhr : xhr,
        });
        xhr.onload = CBAdminPageForModelImport.handleDataFileUploadRequestDidLoad.bind(undefined, {
            enableActionLinkCallback : args.enableActionLinkCallback,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBAdminPageForModelImport&function=uploadDataFile");
        xhr.send(formData);
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForModelImport.DOMContentDidLoad);
