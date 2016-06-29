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

        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";

        var actionLink = CBUIActionLink.create({
            "callback" : input.click.bind(input),
            "labelText" : "Upload CSV Files...",
        });

        input.addEventListener("change", CBAdminPageForModelImport.uploadCSVFiles.bind(undefined, {
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
     * @param function args.disableActionLinkCallback
     * @param function args.enableActionLinkCallback
     * @param Element args.fileInputElement
     *
     * @return undefined
     */
    uploadCSVFiles : function(args) {
        args.disableActionLinkCallback.call();
        window.setTimeout(args.enableActionLinkCallback, 2000);
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForModelImport.DOMContentDidLoad);
