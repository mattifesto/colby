"use strict";

var CBAdminPageForModels = {

    /**
     * @return {Element}
     */
    createUI : function() {
        var element         = document.createElement("div");
        element.className   = "CBAdminPageForModelsUI"
        var menu            = document.createElement("select");

        CBEditableClasses.forEach(function(item) {
            var option          = document.createElement("option");
            option.textContent  = item.title;
            option.value        = item.className;
            menu.appendChild(option);
        });

        element.appendChild(menu);

        return element;
    }
};

(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var main    = document.getElementsByTagName("main")[0];
        var editor  = CBAdminPageForModels.createUI();
        main.appendChild(editor);
    })
})();
