"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISelectableItemContainer */
/* global
    CBUICommandPart,
    CBUITitleAndDescriptionPart */

var CBUISelectableItemContainer = {

    /**
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    create: function () {
        var selectable = false;
        var selectionChangedCallback;
        var element = document.createElement("div");
        element.className = "CBUISelectableItemContainer";
        var containerElement = document.createElement("div");
        containerElement.className = "container";

        var headerElement = document.createElement("div");
        headerElement.className = "header";

        var titlePart = CBUITitleAndDescriptionPart.create();
        titlePart.title = "Views";

        headerElement.appendChild(titlePart.element);

        var editCommand = CBUICommandPart.create();
        editCommand.title = "Edit";
        editCommand.callback = function () {
            if (selectable) {
                editCommand.title = "Edit";
                o.selectable = false;
            } else {
                editCommand.title = "Done";
                o.selectable = true;
            }
        };

        headerElement.appendChild(editCommand.element);

        var itemsElement = document.createElement("div");
        itemsElement.className = "items";

        var footerElement = document.createElement("div");
        footerElement.className = "footer";

        containerElement.appendChild(headerElement);
        containerElement.appendChild(itemsElement);
        containerElement.appendChild(footerElement);
        element.appendChild(containerElement);

        var o = {
            commands: {

                /**
                 * @param CBUICommandPart commandPart
                 *
                 * @return undefined
                 */
                push: function (commandPart) {
                    footerElement.appendChild(commandPart.element);
                }
            },

            /**
             * @return Element
             */
            get element() {
                return element;
            },

            /**
             * @param int index
             *
             * @return CBUISelectableItem
             */
            item: function(index) {
                return itemsElement.children.item(index).CBUISelectableItem;
            },

            /**
             * @return int
             */
            get length() {
                return itemsElement.children.length;
            },

            /**
             * @param CBUISelectableItem selectableItem
             *
             * @return undefined
             */
            push: function (selectableItem) {
                o.splice(o.length, 0, selectableItem);
            },

            /**
             * @return bool
             */
            get selectable() {
                return selectable;
            },

            /**
             * @param bool value
             */
            set selectable(value) {
                if (value) {
                    element.classList.add("selectable");

                    for (let i = 0; i < itemsElement.children.length; i++) {
                        itemsElement.children[i].CBUISelectableItem.selectable = true;
                    }

                    selectable = true;
                } else {
                    element.classList.remove("selectable");

                    for (let i = 0; i < itemsElement.children.length; i++) {
                        itemsElement.children[i].CBUISelectableItem.selectable = false;
                    }

                    selectable = false;
                }

                if (selectionChangedCallback !== undefined) {
                    selectionChangedCallback();
                }
            },

            /**
             * @return function
             */
            get selectionChangedCallback() {
                return selectionChangedCallback;
            },

            /**
             * @param function callback
             *
             *      Selection changed is not guranteed. This callback will be
             *      called at times when the actual selection hasn't changed.
             *
             *      For instance, when the container is switched to edit mode
             *      it will be called even though the actual selected items
             *      haven't changed.
             */
            set selectionChangedCallback(callback) {
                if (typeof callback === "function") {
                    selectionChangedCallback = callback;
                } else {
                    selectionChangedCallback = undefined;
                }
            },

            /**
             * @param int startIndex
             * @param int deleteCount
             * @param CBUISelectableItem selectableItem
             *
             * @return [CBUISelectableItem]
             */
            splice: function (startIndex, deleteCount, selectableItem1) {
                var removedElements = [];

                if (deleteCount == 1) {
                    let length = itemsElement.children.length;

                    if (startIndex < length) {
                        let element = itemsElement.children.item(startIndex);

                        removedElements.push(element);
                        itemsElement.removeChild(element);
                    }
                }

                if (selectableItem1 !== undefined) {
                    selectableItem1.selectable = selectable;

                    let length = itemsElement.children.length;
                    let referenceNode = null;

                    if (startIndex < length) {
                        referenceNode = itemsElement.children.item(startIndex);
                    }

                    itemsElement.insertBefore(selectableItem1.element, referenceNode);
                }

                return removedElements.map(element => element.CBUISelectableItem);
            },
        };

        element.CBUISelectableItemContainer = o;

        return o;
    },
};
