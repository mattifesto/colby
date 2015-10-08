"use strict";

var CBUnpublishedPagesAdmin = {

    /**
     * @return {Element}
     */
    createElement : function() {
        var element = document.createElement("div");
        element.className = "CBUnpublishedPagesAdmin";
        element.textContent = "foo";

        return element;
    }
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBUnpublishedPagesAdmin.createElement());
});
