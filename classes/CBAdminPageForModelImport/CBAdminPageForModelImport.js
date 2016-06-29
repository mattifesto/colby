"use strict"; /* jshint strict: global */
/* globals CBUI, CBUIActionLink */

var CBAdminPageForModelImport = {

    /**
     * @return undefined
     */
    DOMContentDidLoad : function() {
        var main = document.getElementsByTagName("main")[0];
        var element = document.createElement("div");
        element.className = "CBAdminPageForModelImport";

        element.appendChild(CBUI.createHalfSpace());

        var section = CBUI.createSection();
        var item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            "callback" : CBAdminPageForModelImport.uploadCSVFiles.bind(undefined, {

            }),
            "labelText" : "Upload CSV Files...",
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        main.appendChild(element);
    },

    /**
     * @return undefined
     */
    uploadCSVFiles : function(args) {
        alert("foo");
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForModelImport.DOMContentDidLoad);
