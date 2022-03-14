/* global
    CBUICommandPart,
    CBUIStringsPart,
*/

(function () {
    "use strict";

    window.CBUISelectableItemContainer =
    {
        create: CBUISelectableItemContainer_create,
    };



    /**
     * @return object
     *
     *      {
     *          commands: {
     *              push(CBUICommandPart commandPart)
     *          }
     *          element: Element
     *          item(int index) -> CBUISelectableItem
     *          length: int (readonly)
     *          push(CBUISelectableItem selectableItem)
     *          selectable: bool
     *
     *              Changes the container from normal mode to selectable mode.
     *
     *          selectionChangedCallback: function
     *
     *          splice(
     *              int startIndex,
     *              int deleteCount,
     *              CBUISelectableItem selectableItem1
     *          ) -> [CBUISelectableItem]
     *
     *          title: string
     *      }
     */
    function
    CBUISelectableItemContainer_create(
    ) // -> object
    {
        let selectable =
        false;

        let selectionChangedCallback;

        let element =
        document.createElement(
            "div"
        );

        element.className =
        "CBUISelectableItemContainer";

        let containerElement =
        document.createElement(
            "div"
        );

        containerElement.className =
        "container";

        let headerElement =
        document.createElement(
            "div"
        );

        headerElement.className =
        "header";

        let stringsPart =
        CBUIStringsPart.create();

        headerElement.appendChild(
            stringsPart.element
        );

        let headerCommandsElement =
        document.createElement(
            "div"
        );

        headerCommandsElement.className =
        "commands";

        headerElement.appendChild(
            headerCommandsElement
        );

        let selectAllCommand =
        CBUICommandPart.create();

        selectAllCommand.disabled =
        true;

        selectAllCommand.title =
        "All";

        selectAllCommand.callback =
        function ()
        {
            let length =
            itemsElement.children.length;

            for (
                let i = 0;
                i < length;
                i++)
            {
                itemsElement.children[i].CBUISelectableItem.selected =
                true;
            }
        };

        headerCommandsElement.appendChild(
            selectAllCommand.element
        );

        let selectNoneCommand =
        CBUICommandPart.create();

        selectNoneCommand.disabled =
        true;

        selectNoneCommand.title =
        "None";

        selectNoneCommand.callback =
        function ()
        {
            let length =
            itemsElement.children.length;

            for (
                let i = 0;
                i < length;
                i++
            ) {
                itemsElement.children[i].CBUISelectableItem.selected =
                false;
            }
        };

        headerCommandsElement.appendChild(
            selectNoneCommand.element
        );

        let editCommand =
        CBUICommandPart.create();

        editCommand.title =
        "Edit";

        editCommand.callback =
        function ()
        {
            if (
                selectable
            ) {
                editCommand.title =
                "Edit";

                selectAllCommand.disabled =
                true;

                selectNoneCommand.disabled =
                true;

                o.selectable =
                false;
            }

            else
            {
                editCommand.title =
                "Done";

                selectAllCommand.disabled =
                false;

                selectNoneCommand.disabled =
                false;

                o.selectable =
                true;
            }
        };

        headerCommandsElement.appendChild(
            editCommand.element
        );

        let itemsElement =
        document.createElement(
            "div"
        );

        itemsElement.className =
        "items";

        let footerElement =
        document.createElement(
            "div"
        );

        footerElement.className =
        "footer";

        containerElement.appendChild(
            headerElement
        );

        containerElement.appendChild(
            itemsElement
        );

        containerElement.appendChild(
            footerElement
        );

        element.appendChild(
            containerElement
        );

        let o = {
            commands:
            {

                /**
                 * @param CBUICommandPart commandPart
                 *
                 * @return undefined
                 */
                push:
                function (
                    commandPart
                )
                {
                    footerElement.appendChild(
                        commandPart.element
                    );
                }
            },

            /**
             * @return Element
             */
            get element()
            {
                return element;
            },

            /**
             * @param int index
             *
             * @return CBUISelectableItem
             */
            item:
            function (
                index
            )
            {
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
            push:
            function (
                selectableItem
            ) {
                o.splice(
                    o.length,
                    0,
                    selectableItem
                );
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
            set selectable(
                value
            )
            {
                if (
                    value
                ) {
                    element.classList.add(
                        "selectable"
                    );

                    for (
                        let i = 0;
                        i < itemsElement.children.length;
                        i++
                    ) {
                        itemsElement.children[i].CBUISelectableItem.selectable =
                        true;
                    }

                    selectable =
                    true;
                } else {
                    element.classList.remove(
                        "selectable"
                    );

                    for (
                        let i = 0;
                        i < itemsElement.children.length;
                        i++
                    ) {
                        itemsElement.children[i].CBUISelectableItem.selectable =
                        false;
                    }

                    selectable =
                    false;
                }

                if (
                    selectionChangedCallback !== undefined
                ) {
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
            set selectionChangedCallback(
                callback
            )
            {
                if (
                    typeof callback === "function"
                ) {
                    selectionChangedCallback =
                    callback;
                }

                else {
                    selectionChangedCallback =
                    undefined;
                }
            },

            /**
             * @param int startIndex
             * @param int deleteCount
             * @param CBUISelectableItem selectableItem
             *
             * @return [CBUISelectableItem]
             */
            splice:
            function (
                startIndex,
                deleteCount,
                selectableItem1
            )
            {
                let removedElements =
                [];

                if (
                    deleteCount == 1
                ) {
                    let length =
                    itemsElement.children.length;

                    if (
                        startIndex < length
                    ) {
                        let element =
                        itemsElement.children.item(
                            startIndex
                        );

                        removedElements.push(
                            element
                        );

                        itemsElement.removeChild(
                            element
                        );
                    }
                }

                if (
                    selectableItem1 !== undefined
                ) {
                    selectableItem1.selectable =
                    selectable;

                    let length =
                    itemsElement.children.length;

                    let referenceNode =
                    null;

                    if (
                        startIndex < length
                    ) {
                        referenceNode =
                        itemsElement.children.item(
                            startIndex
                        );
                    }

                    itemsElement.insertBefore(
                        selectableItem1.element,
                        referenceNode
                    );
                }

                return removedElements.map(
                    element => element.CBUISelectableItem
                );
            },

            /**
             * @return string
             */
            get title() {
                return stringsPart.string1;
            },

            /**
             * @param string value
             */
            set title(
                value
            )
            {
                stringsPart.string1 = value;
            },
        };

        element.CBUISelectableItemContainer =
        o;

        return o;
    }
    // CBUISelectableItemContainer_create()

})();
