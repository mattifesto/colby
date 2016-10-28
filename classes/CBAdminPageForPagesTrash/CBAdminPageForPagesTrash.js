"use strict"; /* jshint strict: global */
/* global CBUI, Colby */

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
                args.section.appendChild(CBUI.createSectionItemWithCommands({
                    title : model.title,
                    callback : function () { alert("title"); },
                    commands : [{
                        title : "Delete",
                        callback : function () { alert("delete"); },
                    },{
                        title : "Recover",
                        callback : function () { alert("recover"); },
                    }],
                }).element);
            });
        } else {
            Colby.displayResponse(response);
        }
    },
};

document.addEventListener("DOMContentLoaded", function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForPagesTrash.createElement());
});
