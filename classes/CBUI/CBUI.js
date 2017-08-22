"use strict"; /* jshint strict: global */
/* global CBUIDropdown */

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
     * @param string? args.key
     * @param string? args.value
     *
     * @return {
     *      Element element,
     * }
     */
    createKeyValueSectionItem: function (args) {
        var item = CBUI.createSectionItem();
        var keyValue = document.createElement("div");
        keyValue.className = "CBUIKeyValue";
        var key = document.createElement("div");
        key.className = "key";
        key.textContent = args.key || "";
        var value = document.createElement("div");
        value.className = "value";
        value.textContent = args.value || "";

        keyValue.appendChild(key);
        keyValue.appendChild(value);
        item.appendChild(keyValue);

        return {
            element: item,
        };
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
     * Creates a section header with an optional title and description. This is
     * use to add inline context for the user interface.
     *
     * @param [string]? args.paragraphs
     *
     *      An array of paragraphs providing more description content for the
     *      header.
     *
     * @param string? args.text
     *
     *      The title. This is named "text" because it was originally the only
     *      supported property.
     *
     * @return Element
     */
    createSectionHeader : function (args) {
        var element = document.createElement("header");
        element.className = "CBUISectionHeader";

        if (args.text) {
            var title = document.createElement("h1");
            title.textContent = args.text;

            element.appendChild(title);
        }


        if (Array.isArray(args.paragraphs)) {
            var description = document.createElement("div");

            args.paragraphs.forEach(function (paragraph) {
                var p = document.createElement("p");
                p.textContent = paragraph;
                description.appendChild(p);
            });

            element.appendChild(description);
        }

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
     * @return {
     *  Element element
     *      The section item element.
     *  Element titleElement
     *      You can either add child elements to this or just set the
     *      textContent property.
     *  Element commandsElement
     *      Add subelements with click event handlers to this element. A simple
     *      <div> with text and a className of "command" will be given the
     *      standard basic styles of a command. Or you can add custom command
     *      elements.
     *  function setThumbnailURI
     * }
     */
    createSectionItem2 : function () {
        var element = CBUI.createSectionItem();
        element.classList.add("CBUISectionItem2");
        var thumbnailElement = document.createElement("div");
        thumbnailElement.className = "thumbnail";
        var imageElement = document.createElement("img");
        var titleElement = document.createElement("div");
        titleElement.className = "title";
        var dropdown = CBUIDropdown.create();

        thumbnailElement.appendChild(imageElement);
        element.appendChild(thumbnailElement);
        element.appendChild(titleElement);
        element.appendChild(dropdown.dropdownElement);

        function setThumbnailURI(URI) {
            if (URI) {
                thumbnailElement.classList.add("set");
                imageElement.src = URI;
            } else {
                thumbnailElement.classList.remove("set");
                imageElement.src = "";
            }
        }

        return {
            element: element,
            titleElement: titleElement,
            commandsElement: dropdown.menuElement,
            setThumbnailURI: setThumbnailURI,
        };
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
