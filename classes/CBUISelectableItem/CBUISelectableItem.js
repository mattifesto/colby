"use strict";
/* jshint strict: global */
/* exported CBUISelectableItem */

var CBUISelectableItem = {

    /**
     * @return object
     *
     *      {
     *          callback: function
     *          element: Element (readonly)
     *          push(part)
     *          selectable: bool
     *          seleted: bool
     *      }
     */
    create: function () {
        var callback;
        var selectable = false;
        var selected = false;
        var element = document.createElement("div");
        element.className = "CBUISelectableItem";

        element.addEventListener("click", function () {
            if (selectable) {
                o.selected = !selected;
            } else if (callback) {
                callback();
            }
        });

        var selectorPart = document.createElement("div");
        selectorPart.className = "selectorPart";

        element.appendChild(selectorPart);

        var selectorBox = document.createElement("div");
        selectorBox.className = "box";
        selectorBox.textContent = "✔";

        selectorPart.appendChild(selectorBox);
        
        var partsElement = document.createElement("div");
        partsElement.className = "parts";

        element.appendChild(partsElement);

        var o = {
            get callback() {
                return callback;
            },
            set callback(value) {
                if (typeof value === "function") {
                    callback = value;
                }
            },
            get element() {
                return element;
            },
            push: function (part) {
                partsElement.appendChild(part.element);
            },
            get selectable() {
                return selectable;
            },
            set selectable(value) {
                if (value) {
                    element.classList.add("selectable");
                    selectable = true;
                } else {
                    o.selected = false;
                    element.classList.remove("selectable");
                    selectable = false;
                }
            },
            get selected() {
                return selected;
            },
            set selected(value) {
                if (value) {
                    element.classList.add("selected");
                    selected = true;
                } else {
                    element.classList.remove("selected");
                    selected = false;
                }
            }
        };

        element.CBUISelectableItem = o;

        return o;
    },
};
