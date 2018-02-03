"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPagesTrashAdmin */
/* global
    CBUI,
    Colby */

var CBPagesTrashAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(CBUI.createHalfSpace());
        mainElement.appendChild(CBUI.createSectionHeader({
            text: "Pages in the trash",
        }));

        let sectionElement = CBUI.createSection();

        mainElement.appendChild(sectionElement);
        mainElement.appendChild(CBUI.createHalfSpace());

        Colby.callAjaxFunction("CBPagesTrashAdmin", "fetchPages")
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        /* closure */
        function onFulfilled(pages) {
            pages.forEach(function (page) {
                var sectionItem = CBUI.createSectionItem2();
                sectionItem.titleElement.textContent = page.title;

                sectionItem.titleElement.addEventListener("click", function() {
                    window.location = "/admin/?c=CBModelInspector&ID=" + page.ID;
                });

                sectionElement.appendChild(sectionItem.element);

                var recoverCommand = document.createElement("div");
                recoverCommand.className = "command";
                recoverCommand.textContent = "Recover";
                recoverCommand.addEventListener("click", CBPagesTrashAdmin.recoverPage.bind(undefined, {
                    ID: page.ID,
                    sectionItemElement: sectionItem.element,
                }));

                sectionItem.commandsElement.appendChild(recoverCommand);

                var deleteCommand = document.createElement("div");
                deleteCommand.className = "command";
                deleteCommand.textContent = "Delete";
                deleteCommand.addEventListener("click", function () {
                    Colby.callAjaxFunction("CBModels", "deleteByID", { ID: page.ID })
                        .then(onDeleteFulfilled)
                        .catch(Colby.displayAndReportError);
                });

                function onDeleteFulfilled() {
                    sectionItem.element.parentElement.removeChild(sectionItem.element);
                    Colby.alert("The page was successfully deleted.");
                }

                sectionItem.commandsElement.appendChild(deleteCommand);
            });
        }
    },

    /**
     * @param hex160 args.ID
     * @param Element args.sectionItemElement
     *
     * @return undefined
     */
    recoverPage: function (args) {
        var formData = new FormData();
        formData.append("ID", args.ID);

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBPagesTrashAdmin.recoverPageDidLoad.bind(undefined, {
            sectionItemElement: args.sectionItemElement,
            xhr: xhr,
        });
        xhr.open("POST", "/api/?class=CBPagesTrashAdmin&function=recoverPage");
        xhr.send(formData);
    },

    /**
     * @param Element args.sectionItemElement
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    recoverPageDidLoad: function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.sectionItemElement.parentElement.removeChild(args.sectionItemElement);
        } else {
            Colby.displayResponse(response);
        }
    },
};

Colby.afterDOMContentLoaded(CBPagesTrashAdmin.init);
