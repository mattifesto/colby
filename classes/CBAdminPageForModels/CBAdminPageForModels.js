"use strict";

var CBAdminPageForModels = {

    createEditor : function() {
        var element = document.createElement("div");
        element.textContent = "Hello, world!";

        return element;
    }
};

(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var main    = document.getElementsByTagName("main")[0];
        var editor  = CBAdminPageForModels.createEditor();
        main.appendChild(editor);
    })
})();
