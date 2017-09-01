"use strict"; /* jshint strict: global */
/* globals
    CBUI,
    CBUIActionLink,
    CBUITaskStatus,
    Colby */

var CBAdminPageForModelImport = {

    /**
     * @return undefined
     */
    DOMContentDidLoad : function() {
        var section, item;
        var main = document.getElementsByTagName("main")[0];
        var element = document.createElement("div");
        element.className = "CBAdminPageForModelImport";

        /* import JSON */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            paragraphs: ["Import Single Model"],
        }));
        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        var jsonInputElement = document.createElement("input");
        jsonInputElement.type = "file";
        jsonInputElement.style.display = "none";
        var jsonActionLink = CBUIActionLink.create({
            callback: jsonInputElement.click.bind(jsonInputElement),
            labelText: "Import JSON File...",
        });

        jsonInputElement.addEventListener("change", function() {
            var formData = new FormData();
            formData.append("file", jsonInputElement.files[0]);

            var promise = Colby.fetchAjaxResponse("/api/?class=CBAdminPageForModelImport&function=importJSON", formData)
                .then(onFulfilled)
                .catch(Colby.displayError)
                .then(onFinally);

            Colby.retain(promise);

            jsonInputElement.value = null;

            function onFulfilled(response) {
                Colby.alert("Imported successfully");
            }

            function onFinally() {
                Colby.release(promise);
            }
        });

        item.appendChild(jsonInputElement);
        item.appendChild(jsonActionLink.element);
        section.appendChild(item);

        element.appendChild(section);

        /* import CSV */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            paragraphs: ["Import Multiple Models"],
        }));

        section = CBUI.createSection();
        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";

        var actionLink = CBUIActionLink.create({
            "callback" : input.click.bind(input),
            "labelText" : "Import CSV File...",
        });

        input.addEventListener("change", CBAdminPageForModelImport.handleFileInputChanged.bind(undefined, {
            disableActionLinkCallback : actionLink.disableCallback,
            enableActionLinkCallback : actionLink.enableCallback,
            fileInputElement : input,
        }));

        element.appendChild(input);
        item = CBUI.createSectionItem();
        item.appendChild(actionLink.element);

        section.appendChild(item);

        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUITaskStatus.create().element);

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

        if (response.wasSuccessful) {
            Colby.doTask();
        } else {
            Colby.displayResponse(response);
        }

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
