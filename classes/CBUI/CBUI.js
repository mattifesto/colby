"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUI */
/* global
    CBMessageMarkup,
    CBUIDropdown,
    CBUISection,
*/

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
     * @param object args
     *
     *      {
     *          centerElement: Element
     *
     *              This element is usually created using the
     *              CBUI.createHeaderTitle() function.
     *
     *          leftElements: [Element]
     *          rightElements: [Element]
     *
     *              The elements are usually created using the
     *              CBUI.createHeaderButtonItem() function.
     *
     *          leftElement: Element (deprecated)
     *          rightElement: Element (deprecated)
     *      }
     *
     * @return Element
     */
    createHeader: function (args) {
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
     * @deprecated use createHeaderItem()
     *
     * @param object args
     *
     *      {
     *          callback: function?
     *          text: string?
     *      }
     *
     * @return Element
     */
    createHeaderButtonItem: function (args) {
        let item = CBUI.createHeaderItem();
        item.textContent = args.text;
        item.callback = args.callback;

        return item.element;
    },

    /**
    * @deprecated use createHeaderItem()
    *
    * @param object args
    *
    *      {
    *          text: string?
    *      }
    *
     * @return Element
     */
    createHeaderTitle: function (args) {
        let item = CBUI.createHeaderItem();
        item.textContent = args.text;

        return item.element;
    },

    /**
     * @return object
     *
     *      {
     *          callback: function (get, set)
     *          element: Element (get)
     *          href: string (get, set)
     *          textContent: string (get, set)
     *      }
     */
    createHeaderItem: function () {
        let callback;
        let element = document.createElement("div");
        element.className = "CBUIHeaderItem";
        let content = document.createElement("a");

        element.appendChild(content);

        content.addEventListener("click", function (event) {
            if (typeof callback === "function") {

                /**
                 * If there is a callback then don't do what the anchor would
                 * normally do (navigate to a page).
                 */
                event.preventDefault();

                callback();
            }
        });

        return {
            get callback() {
                return callback;
            },
            set callback(value) {
                content.href = "";

                if (typeof value === "function") {
                    callback = value;
                    element.classList.add("action");
                } else {
                    callback = undefined;
                    element.classList.remove("action");
                }
            },
            get element() {
                return element;
            },
            get href() {
                return content.href;
            },
            set href(value) {
                callback = undefined;

                if (typeof value === "string") {
                    content.href = value;
                    element.classList.add("action");
                } else {
                    content.href = "";
                    element.classList.remove("action");
                }
            },
            get textContent() {
                return content.textContent;
            },
            set textContent(value) {
                content.textContent = value;
            },
        };
    },

    /**
     * @param object args
     *
     *      {
     *          key: string?
     *          value: string?
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    createKeyValueSectionItem: function (args) {
        var item = CBUI.createSectionItem();
        var keyValue = document.createElement("div");
        keyValue.className = "CBUIKeyValue";
        var key = document.createElement("div");
        key.className = "key";
        key.textContent = args.key || "\xa0";
        var value = document.createElement("div");
        value.className = "value";
        value.textContent = args.value || "\xa0";

        keyValue.appendChild(key);
        keyValue.appendChild(value);
        item.appendChild(keyValue);

        return {
            element: item,
        };
    },

    /**
     * @param object args
     *
     *      {
     *          message: string? (message markup)
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    createMessageSectionItemPart: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIMessageSectionItemPart CBContentStyleSheet";
        element.innerHTML = CBMessageMarkup.convert(args.message || "");

        return {
            element: element,
        };
    },

    /**
     * @deprecated use CBUISection.create()
     *
     * @return Element
     */
    createSection: function () {
        let section = CBUISection.create();

        return section.element;
    },

    /**
     * Creates a section header with an optional title and description. This is
     * use to add inline context for the user interface.
     *
     * @param object args
     *
     *      {
     *          paragraphs: [string]?
     *
     *              An array of paragraphs providing more description content
     *              for the header.
     *
     *          text: string?
     *
     *              The title. This is named "text" because it was originally
     *              the only supported property.
     *      }
     *
     * @return Element
     */
    createSectionHeader: function (args) {
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
     * @return object
     *
     *      {
     *          element: Element
     *          titleElement: Element
     *
     *              You can either add child elements to this or just set the
     *              textContent property. You can also add click event handlers
     *              to this element.
     *
     *          commandsElement: Element
     *
     *              Add subelements with click event handlers to this element. A
     *              simple <div> with text and a className of "command" will be
     *              given the standard basic styles of a command. Or you can add
     *              custom command elements.
     *
     *          setThumbnailURI: function
     *      }
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
};
