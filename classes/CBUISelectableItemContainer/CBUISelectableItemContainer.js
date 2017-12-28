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
        var element = document.createElement("div");
        element.className = "CBUISelectableItemContainer";
        var containerElement = document.createElement("div");
        containerElement.className = "container";

        var headerElement = document.createElement("div");
        headerElement.className = "header";

        var titlePart = CBUITitleAndDescriptionPart.create();
        titlePart.title = "Views";

        headerElement.appendChild(titlePart.element);

        var selectCommand = CBUICommandPart.create();
        selectCommand.title = "Edit";
        selectCommand.callback = function () {
            if (selectable) {
                selectCommand.title = "Edit";
                o.selectable = false;
            } else {
                selectCommand.title = "Done";
                o.selectable = true;
            }
        };

        headerElement.appendChild(selectCommand.element);

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
                    commandPart.disabled = !selectable;

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

                    for (let i = 0; i < footerElement.children.length; i++) {
                        footerElement.children[i].CBUICommandPart.disabled = false;
                    }

                    selectable = true;
                } else {
                    element.classList.remove("selectable");

                    for (let i = 0; i < itemsElement.children.length; i++) {
                        itemsElement.children[i].CBUISelectableItem.selectable = false;
                    }

                    for (let i = 0; i < footerElement.children.length; i++) {
                        footerElement.children[i].CBUICommandPart.disabled = true;
                    }

                    selectable = false;
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
