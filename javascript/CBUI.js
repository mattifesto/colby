"use strict";

var CBUI = {

    /**
     * @return Element
     */
    createHalfSpace : function() {
        var element = document.createElement("div");
        element.className = "CBUIHalfSpace";

        return element;
    },

    /**
     * @param Element args.centerElement
     * @param Element args.leftElement
     * @param Element args.rightElement
     *
     * @return Element
     */
    createHeader : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIHeader";

        var left = document.createElement("div");
        left.className = "left";

        element.appendChild(left);

        var center = document.createElement("div");
        center.className = "center";

        if (args.centerElement) {
            center.appendChild(args.centerElement);
        }

        element.appendChild(center);

        var right = document.createElement("div");
        right.className = "right";

        if (args.rightElement) {
            right.appendChild(args.rightElement);
        }

        element.appendChild(right);

        return element;
    },

    /**
     * @return Element
     */
    createSection : function () {
        var element = document.createElement("div");
        element.className = "CBUISection";

        return element;
    },

    /**
     * @param string args.text
     *
     * @return Element
     */
    createSectionHeader : function (args) {
        var element = document.createElement("div");
        element.className = "CBUISectionHeader";
        element.textContent = args.text;

        return element;
    },

    /**
     * @return Element
     */
    createSectionItem : function () {
        var element = document.createElement("div");
        element.className = "CBUISectionItem";

        return element;
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBUI.css";

    document.head.appendChild(link);
})();
