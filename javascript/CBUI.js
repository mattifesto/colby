"use strict";

var CBUI = {

    /**
     * @param function args.buttonClickedCallback
     * @param string args.text
     *
     * @return {
     *  Element element,
     *  function updateTextCallback
     * }
     */
    createButton : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIButton";
        var updateTextCallback = CBUI.updateTextContent.bind(undefined, element);

        updateTextCallback.call(undefined, args.text);

        element.addEventListener("click", args.buttonClickedCallback);

        return {
            element : element,
            updateTextCallback : updateTextCallback,
        };
    },

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
     * @param Element args.leftElement (deprecated)
     * @param [Element] args.leftElements
     * @param Element args.rightElement (deprecated)
     * @param [Element] args.rightElements
     *
     * @return Element
     */
    createHeader : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIHeader";

        /* left */
        var left = document.createElement("div");
        left.className = "left";
        if (args.leftElements) {
            args.leftElements.forEach(function (element) {
                left.appendChild(element);
            });
        } else if (args.leftElement) {
            left.appendChild(args.leftElement);
        }
        element.appendChild(left);

        /* center */
        var center = document.createElement("div");
        center.className = "center";
        if (args.centerElement) {
            center.appendChild(args.centerElement);
        }
        element.appendChild(center);

        /* right */
        var right = document.createElement("div");
        right.className = "right";
        if (args.rightElements) {
            args.rightElements.forEach(function (element) {
                right.appendChild(element);
            });
        } else if (args.rightElement) {
            right.appendChild(args.rightElement);
        }
        element.appendChild(right);

        return element;
    },

    /**
     * @param function? args.callback
     * @param string? args.text
     *
     * @return Element
     */
    createHeaderButtonItem : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIHeaderButtonItem";
        element.textContent = args.text || null;

        if (args.callback) {
            element.addEventListener("click", args.callback);
        }

        return element;
    },

    /**
     * @param string text
     *
     * @return Element
     */
    createHeaderTitle : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIHeaderTitle";
        element.textContent = args.text || null;

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

    /**
     * @param Element element
     * @param string text
     *
     * @return undefined
     */
    updateTextContent : function (element, text) {
        element.textContent = text;
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBUI.css";

    document.head.appendChild(link);
})();
