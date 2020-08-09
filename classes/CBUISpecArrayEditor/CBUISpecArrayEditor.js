"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpecArrayEditor */
/* global
    CBException,
    CBUI,
    CBUICommandPart,
    CBUINavigationView,
    CBUIPanel,
    CBUISelectableItem,
    CBUISelectableItemContainer,
    CBUISelector,
    CBUISpec,
    CBUISpecClipboard,
    CBUISpecEditor,
    CBUIThumbnailPart,
    CBUITitleAndDescriptionPart,
    Colby,
*/



var CBUISpecArrayEditor = {


    /**
     * @param object args
     *
     *      {
     *          addableClassNames: [string]
     *          specs: [object]
     *          specsChangedCallback: function
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *
     *          title: string
     *
     *              @deprecated 2019_06_26
     *
     *              The caller should add a CBUI_title1 element before the
     *              editor element.
     *      }
     */
    create: function (
        args
    ) {
        if (!Array.isArray(args.addableClassNames)) {
            throw CBException.withError(
                Error(
                    "The addableClassNames parameter must be an array."
                ),
                "",
                "fc964668d6d197dda3d1d5327e307eeab0a787ac"
            );
        }

        let addableClassNames = args.addableClassNames;
        var specs = args.specs;
        var specsChangedCallback = args.specsChangedCallback;

        let element = CBUI.createElement(
            "CBUISpecArrayEditor CBUI_sectionContainer"
        );

        let selectableItemContainer = CBUISelectableItemContainer.create();
        selectableItemContainer.selectionChangedCallback = selectionChanged;

        let addCommand = CBUICommandPart.create();
        addCommand.title = "Add";

        addCommand.callback = function () {
            requestClassName().then(
                function (className) {
                    return add(className);
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayError(error);
                    Colby.reportError(error);
                }
            );

            return;



            /* --  closures -- -- -- -- -- */



            /**
             * @param string className
             *
             * @return undefined
             */
            function add(
                className
            ) {
                /**
                 * @NOTE 2020_07_29
                 *
                 *      I'm not sure why we just return instead of throwing an
                 *      error here. I literally have a situation where this is
                 *      happening and would have appreciated an error. I'm not
                 *      changing the code now because I have too many other
                 *      tasks to do.
                 */
                if (className === undefined) {
                    return;
                }

                let length = selectableItemContainer.length;
                let pasteIndex = length;

                for (let i = 0; i < length; i++) {
                    let selectableItem = selectableItemContainer.item(i);

                    if (selectableItem.selected) {
                        pasteIndex = i;
                        break;
                    }
                }

                let spec = {
                    className: className,
                };

                let selectableItem = specToSelectableItem(spec);

                selectableItemContainer.splice(
                    pasteIndex,
                    0,
                    selectableItem
                );

                specs.splice(
                    pasteIndex,
                    0,
                    spec
                );

                specsChangedCallback();
            }
            /* add() */

        };
        /* addCommand.callback */


        selectableItemContainer.commands.push(addCommand);

        let cutCommand = CBUICommandPart.create();
        cutCommand.title = "Cut";
        cutCommand.callback = function () {
            copySelectedItems();

            let count = 0;
            let i = 0;
            let length = selectableItemContainer.length;

            while (i < length) {
                let selectableItem = selectableItemContainer.item(i);

                if (selectableItem.selected) {
                    selectableItemContainer.splice(i, 1);
                    specs.splice(i, 1);

                    count++;
                    length--;

                    continue;
                }

                i++;
            }

            if (count > 0) {
                specsChangedCallback();
            }
        };

        selectableItemContainer.commands.push(cutCommand);

        let copyCommand = CBUICommandPart.create();
        copyCommand.title = "Copy";

        copyCommand.callback = function () {
            let count = copySelectedItems();

            CBUIPanel.displayText(
                count +
                " items were copied to the clipboard"
            );
        };

        selectableItemContainer.commands.push(copyCommand);

        let pasteCommand = CBUICommandPart.create();
        pasteCommand.title = "Paste";
        pasteCommand.callback = function () {
            let length = selectableItemContainer.length;
            let pasteIndex = length;

            for (let i = 0; i < length; i++) {
                let selectableItem = selectableItemContainer.item(i);

                if (selectableItem.selected) {
                    pasteIndex = i;
                    break;
                }
            }

            let clipboardSpecs = CBUISpecClipboard.specs;

            for (let i = 0; i < clipboardSpecs.length; i++) {
                let spec = clipboardSpecs[i];
                let selectableItem = specToSelectableItem(spec);

                selectableItemContainer.splice(pasteIndex, 0, selectableItem);
                specs.splice(pasteIndex, 0, spec);

                pasteIndex++;
            }

            if (clipboardSpecs.length > 0) {
                specsChangedCallback();
            }
        };

        selectableItemContainer.commands.push(pasteCommand);

        let upCommand = CBUICommandPart.create();
        upCommand.title = "▲";
        upCommand.callback = function () {
            let i = 1; // start at 1 because item at index 0 can't be moved up
            let length = selectableItemContainer.length;

            while (i < length) {
                let selectableItem = selectableItemContainer.item(i);

                if (selectableItem.selected) {
                    let previousSelectableItem =
                    selectableItemContainer.item(i - 1);

                    if (!previousSelectableItem.selected) {
                        let removedSelectableItems =
                        selectableItemContainer.splice(
                            i,
                            1
                        );

                        selectableItemContainer.splice(
                            i - 1,
                            0,
                            removedSelectableItems[0]
                        );

                        let removedSpecs = specs.splice(i, 1);
                        specs.splice(i - 1, 0, removedSpecs[0]);

                        specsChangedCallback();
                    }
                }

                i++;
            }
        };

        selectableItemContainer.commands.push(upCommand);

        let downCommand = CBUICommandPart.create();
        downCommand.title = "▼";
        downCommand.callback = function () {
            let length = selectableItemContainer.length;

            // start at second to last because las can't be moved down
            let i = length - 2;

            while (i >= 0) {
                let selectableItem = selectableItemContainer.item(i);

                if (selectableItem.selected) {
                    let nextSelectableItem =
                    selectableItemContainer.item(i + 1);

                    if (!nextSelectableItem.selected) {
                        let removedSelectableItems =
                        selectableItemContainer.splice(
                            i,
                            1
                        );

                        selectableItemContainer.splice(
                            i + 1,
                            0,
                            removedSelectableItems[0]
                        );

                        let removedSpecs = specs.splice(i, 1);
                        specs.splice(i + 1, 0, removedSpecs[0]);

                        specsChangedCallback();
                    }
                }

                i--;
            }
        };

        selectableItemContainer.commands.push(downCommand);

        element.appendChild(selectableItemContainer.element);

        for (let i = 0; i < specs.length; i++) {
            let selectableItem = specToSelectableItem(specs[i]);

            selectableItemContainer.push(selectableItem);
        }

        let api = {

            /**
             * @return Element
             */
            get element() {
                return element;
            },

            /**
             * @return string
             */
            get title() {
                return selectableItemContainer.title;
            },

            /**
             * @param string value
             */
            set title(value) {
                selectableItemContainer.title = value;
            }
        };

        selectionChanged();

        return api;



        /* -- closures -- -- -- -- -- */



        /**
         * @return int
         *
         *      The number of items copied to the clipboard.
         */
        function copySelectedItems() {
            let i = 0;
            let length = selectableItemContainer.length;
            let selectedSpecs = [];

            while (i < length) {
                let selectableItem = selectableItemContainer.item(i);

                if (selectableItem.selected) {
                    selectedSpecs.push(specs[i]);
                }

                i++;
            }

            CBUISpecClipboard.specs = selectedSpecs;

            return selectedSpecs.length;
        }
        /* copySelectedItems() */



        /**
         * @return Promise -> string?
         */
        function requestClassName() {
            let promise = new Promise(
                function (resolve, reject) {
                    requestClassName_initializePromise(resolve, reject);
                }
            );

            return promise;



            /* -- closures -- -- -- -- -- */



            /**
             * @param function resolve
             * @param function reject
             *
             * @return undefined
             */
            function requestClassName_initializePromise(
                resolve
                /* , reject */
            ) {
                if (
                    !Array.isArray(addableClassNames) ||
                    addableClassNames.length === 0
                ) {
                    resolve(undefined);
                } else if (addableClassNames.length === 1) {
                    resolve(addableClassNames[0]);
                } else {
                    let options = addableClassNames.map(
                        function (className) {
                            return {
                                title: className,
                                value: className,
                            };
                        }
                    );

                    CBUISelector.showSelector(
                        {
                            callback: resolve,
                            options: options,
                            selectedValue: undefined,
                            title: "Select a Class Name",
                        }
                    );
                }
            }
            /* requestClassName_initializePromise() */

        }
        /* requestClassName() */



        /**
         * @return undefined
         */
        function selectionChanged() {
            if (selectableItemContainer.selectable) {
                copyCommand.disabled = false;
                cutCommand.disabled = false;
                downCommand.disabled = false;
                upCommand.disabled = false;
            } else {
                copyCommand.disabled = true;
                cutCommand.disabled = true;
                downCommand.disabled = true;
                upCommand.disabled = true;
            }
        }
        /* selectionChanged() */



        /**
         * @param object spec
         *
         * @return CBUISelectableItem
         */
        function specToSelectableItem(spec) {
            let selectableItem = CBUISelectableItem.create();

            selectableItem.callback = function () {
                let editor = CBUISpecEditor.create(
                    {
                        spec: spec,
                        specChangedCallback: specChangedCallback,
                    }
                );

                CBUINavigationView.navigate(
                    {
                        element: editor.element,
                        title: spec.className,
                    }
                );
            };

            let thumbnailPart = CBUIThumbnailPart.create();
            selectableItem.push(thumbnailPart);

            let titleAndDescriptionPart = CBUITitleAndDescriptionPart.create();
            selectableItem.push(titleAndDescriptionPart);

            selectableItem.partsElement.appendChild(
                CBUI.createElement(
                    "CBUI_navigationArrow"
                )
            );

            updateThumbnail();
            updateTitleAndDescription();

            return selectableItem;



            /* -- closures -- -- -- -- -- */



            function specChangedCallback() {
                updateTitleAndDescription();
                updateThumbnail();
                specsChangedCallback();
            }



            function updateTitleAndDescription() {
                let nonBreakingSpace = "\u00A0";

                titleAndDescriptionPart.title = spec.className;

                titleAndDescriptionPart.description =
                CBUISpec.specToDescription(spec) || nonBreakingSpace;
            }



            function updateThumbnail() {
                thumbnailPart.src = CBUISpec.specToThumbnailURL(spec);
            }

        }
        /* specToSelectableItem() */

    },
    /* create() */

};
/* CBUISpecArrayEditor */
