"use strict";
/* jshint strict: global */
/* global
    CBUI,
    Colby */

var CBAdminPageForPagesTrash = {

    /**
     * @return undefined
     */
    createElement : function () {
        var element = document.createElement("div");
        element.className = "CBAdminPageForPagesTrash";

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Pages in the trash",
        }));

        var section = CBUI.createSection();

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        CBAdminPageForPagesTrash.fetchPages({
            section : section,
        });

        return element;
    },

    /**
     * @param Element args.section
     *
     * @return undefined
     */
    fetchPages : function (args) {
        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBAdminPageForPagesTrash.fetchPagesDidLoad.bind(undefined, {
            section : args.section,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBAdminPageForPagesTrash&function=fetchPageSummaryModels");
        xhr.send();
    },

    /**
     * @param Element args.section
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    fetchPagesDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.section.textContent = undefined;

            response.models.forEach(function (model) {
                var sectionItem = CBUI.createSectionItem2();
                sectionItem.titleElement.textContent = model.title;

                sectionItem.titleElement.addEventListener("click", function() {
                    window.location = "/admin/?c=CBModelInspector&ID=" + model.ID;
                });

                args.section.appendChild(sectionItem.element);

                var recoverCommand = document.createElement("div");
                recoverCommand.className = "command";
                recoverCommand.textContent = "Recover";
                recoverCommand.addEventListener("click", CBAdminPageForPagesTrash.recoverPage.bind(undefined, {
                    ID : model.ID,
                    sectionItemElement : sectionItem.element,
                }));

                sectionItem.commandsElement.appendChild(recoverCommand);

                var deleteCommand = document.createElement("div");
                deleteCommand.className = "command";
                deleteCommand.textContent = "Delete";
                deleteCommand.addEventListener("click", function () {
                    Colby.callAjaxFunction("CBModels", "deleteByID", { ID: model.ID })
                        .then(onDeleteFulfilled)
                        .catch(Colby.displayAndReportError);
                });

                function onDeleteFulfilled() {
                    sectionItem.element.parentElement.removeChild(sectionItem.element);
                    Colby.alert("The page was successfully deleted.");
                }

                sectionItem.commandsElement.appendChild(deleteCommand);
            });
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param hex160 args.ID
     * @param Element args.sectionItemElement
     *
     * @return undefined
     */
    recoverPage : function (args) {
        var formData = new FormData();
        formData.append("ID", args.ID);

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBAdminPageForPagesTrash.recoverPageDidLoad.bind(undefined, {
            sectionItemElement : args.sectionItemElement,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBAdminPageForPagesTrash&function=recoverPage");
        xhr.send(formData);
    },

    /**
     * @param Element args.sectionItemElement
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    recoverPageDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.sectionItemElement.parentElement.removeChild(args.sectionItemElement);
        } else {
            Colby.displayResponse(response);
        }
    },
};

document.addEventListener("DOMContentLoaded", function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForPagesTrash.createElement());
});
