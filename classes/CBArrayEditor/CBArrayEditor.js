"use strict";
/* jshint strict: global */
/* exported CBArrayEditor */
/* global
    CBUI,
    CBUIActionLink,
    CBUISelector,
    CBUISpec,
    CBUISpecEditor,
    Colby,
    Promise */

var CBArrayEditor = {

    /**
     * @param object args
     *
     *      {
     *          array: [object]
     *          arrayChangedCallback: function
     *          classNames: [string]
     *          navigateToItemCallback: function
     *          sectionElement: Element
     *      }
     *
     * @param object spec
     *
     * @return undefined
     */
    append: function (args, spec) {
        var element = CBArrayEditor.createSectionItemElement2({
            array: args.array,
            arrayChangedCallback: args.arrayChangedCallback,
            classNames: args.classNames,
            navigateToItemCallback: args.navigateToItemCallback,
            sectionElement: args.sectionElement,
            spec: spec,
        });

        args.array.push(spec);
        args.sectionElement.insertBefore(element, args.sectionElement.lastElementChild.previousSibling);

        args.arrayChangedCallback.call();
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     *
     * @return  undefined
     */
    appendFromClipboardWasClicked: function (args) {
        var specAsJSON = localStorage.getItem("specClipboard");

        if (specAsJSON === null) { return; }

        var spec = JSON.parse(specAsJSON);

        CBArrayEditor.append(args, spec);
    },

    /**
     * @param string className
     *
     * @return object
     */
    classNameToModel: function (className) {
        return {
            className: className,
        };
    },

    /**
     * @param object args
     *
     *      {
     *          array: [object]
     *
     *              The array of specs that will be edited by this editor
     *
     *          arrayChangedCallback: function
     *          classNames: [string]
     *
     *              The class names of specs that are allows to be added to the
     *              array
     *
     *          navigateToItemCallback: function
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        var item;
        var array = args.array;
        var arrayChangedCallback = args.arrayChangedCallback;
        var classNames = args.classNames;
        var navigateToItem = args.navigateToItemCallback;
        var element = document.createElement("div");
        element.className = "CBArrayEditor";

        var sectionElement = document.createElement("div");
        sectionElement.className = "CBUISection";

        args.array.forEach(function (spec) {
            var element = CBArrayEditor.createSectionItemElement2({
                array: array,
                arrayChangedCallback: arrayChangedCallback,
                classNames: classNames,
                navigateToItemCallback: navigateToItem,
                sectionElement: sectionElement,
                spec: spec,
            });

            sectionElement.appendChild(element);
        });

        /* append */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback: appendNew,
            labelText: "Append New...",
        }).element);
        sectionElement.appendChild(item);

        /* closure */
        function appendNew() {
            requestClassName()
                .then(append)
                .catch(Colby.displayAndReportError);

            function requestClassName() {
                return CBArrayEditor.requestModelClassName({
                    classNames: classNames,
                    navigateToItemCallback: navigateToItem,
                });
            }

            function append(className) {
                var newSpec = {
                    className: className,
                };

                var sectionItemElement = CBArrayEditor.createSectionItemElement2({
                    array: array,
                    arrayChangedCallback: arrayChangedCallback,
                    classNames: classNames,
                    navigateToItemCallback: navigateToItem,
                    sectionElement: sectionElement,
                    spec: newSpec,
                });

                array.push(newSpec);
                /* insert before the second to the last child (two action links) */
                sectionElement.insertBefore(sectionItemElement, sectionElement.lastElementChild.previousSibling);

                arrayChangedCallback.call();
            }
        }

        /* append from clipboard */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback: CBArrayEditor.appendFromClipboardWasClicked.bind(undefined, {
                array: array,
                arrayChangedCallback: arrayChangedCallback,
                classNames: classNames,
                navigateToItemCallback: navigateToItem,
                sectionElement: sectionElement,
            }),
            labelText: "Append From Clipboard...",
        }).element);
        sectionElement.appendChild(item);
        element.appendChild(sectionElement);

        return element;
    },

    /**
     * @param object args
     *
     *      {
     *          array: [object]
     *          arrayChangedCallback: function
     *          classNames: [string]
     *          navigateToItemCallback: function
     *          sectionElement: Element
     *          spec: object
     *      }
     *
     * @return Element
     */
    createSectionItemElement2: function (args) {
        var array = args.array;
        var arrayChangedCallback = args.arrayChangedCallback;
        var classNames = args.classNames;
        var sectionElement = args.sectionElement;
        var spec = args.spec;
        var navigateToItem = args.navigateToItemCallback;

        var item = CBUI.createSectionItem2();

        var typeElement = document.createElement("div");
        typeElement.className = "type";
        typeElement.textContent = spec.className;
        item.titleElement.appendChild(typeElement);

        var descriptionElement = document.createElement("div");
        descriptionElement.className = "description";
        item.titleElement.appendChild(descriptionElement);

        /* closure */
        function updateThumbnail() {
            item.setThumbnailURI(CBUISpec.specToThumbnailURI(spec));
        }

        updateThumbnail();

        /* closure */
        function updateDescriptionElement() {
            var nonBreakingSpace = "\u00A0";
            descriptionElement.textContent = CBUISpec.specToDescription(spec) || nonBreakingSpace;
        }

        updateDescriptionElement();

        /* closure */
        function specChangedCallback() {
            updateThumbnail();
            updateDescriptionElement();
            args.arrayChangedCallback();
        }

        /* edit */

        item.titleElement.addEventListener("click", edit);

        /* closure */
        function edit() {
            var editor = CBUISpecEditor.create({
                navigateToItemCallback: navigateToItem,
                spec: spec,
                specChangedCallback: specChangedCallback,
            });

            navigateToItem({
                element: editor.element,
                title: spec.className || "Unknown",
            });
        }

        /* commands */

        var upCommand = document.createElement("div");
        upCommand.className = "command arrange up";
        upCommand.textContent = "Up";
        upCommand.addEventListener("click", moveUp);
        item.commandsElement.appendChild(upCommand);

        /* closure */
        function moveUp() {
            var index = array.indexOf(spec);

            if (index > 0) {
                var itemElement = sectionElement.children.item(index);
                var previousItemElement = itemElement.previousSibling;

                array.splice(index, 1); // remove at index
                array.splice(index - 1, 0, spec); // insert before previous spec

                sectionElement.removeChild(itemElement);
                sectionElement.insertBefore(itemElement, previousItemElement);

                arrayChangedCallback();
            }
        }

        var downCommand = document.createElement("div");
        downCommand.className = "command arrange down optional";
        downCommand.textContent = "Down";
        downCommand.addEventListener("click", moveDown);
        item.commandsElement.appendChild(downCommand);

        /* closure */
        function moveDown() {
            var index = array.indexOf(spec);

            if (index < (array.length - 1)) {
                var itemElement = sectionElement.children.item(index);
                var nextItemElement = itemElement.nextSibling;

                array.splice(index, 1); // remove at index
                array.splice(index + 1, 0, spec); // insert after next spec

                sectionElement.removeChild(itemElement);
                sectionElement.insertBefore(itemElement, nextItemElement.nextSibling);

                arrayChangedCallback();
            }
        }

        var cutCommand = document.createElement("div");
        cutCommand.className = "command edit cut";
        cutCommand.textContent = "Cut";
        cutCommand.addEventListener("click", cut);
        item.commandsElement.appendChild(cutCommand);

        /* closure */
        function cut() {
            if (confirm("Are you sure you want to remove this item?")) {
                var index = array.indexOf(spec);
                var itemElement = sectionElement.children.item(index);

                array.splice(index, 1); // remove at index
                sectionElement.removeChild(itemElement);

                var specAsJSON = JSON.stringify(spec);
                localStorage.setItem("specClipboard", specAsJSON);

                arrayChangedCallback();
            }
        }

        var copyCommand = document.createElement("div");
        copyCommand.className = "command edit copy optional";
        copyCommand.textContent = "Copy";
        copyCommand.addEventListener("click", copy);
        item.commandsElement.appendChild(copyCommand);

        /* closure */
        function copy() {
            var specAsJSON = JSON.stringify(spec);
            localStorage.setItem("specClipboard", specAsJSON);
        }

        var pasteCommand = document.createElement("div");
        pasteCommand.className = "command edit paste";
        pasteCommand.textContent = "Paste";
        pasteCommand.addEventListener("click", paste);
        item.commandsElement.appendChild(pasteCommand);

        function paste() {
            var specAsJSON = localStorage.getItem("specClipboard");

            if (specAsJSON === null) { return; }

            var newSpec = JSON.parse(specAsJSON);
            var indexToInsertBefore = array.indexOf(spec);
            var elementToInsertBefore = sectionElement.children.item(indexToInsertBefore);
            var sectionItemElement = CBArrayEditor.createSectionItemElement2({
                array: array,
                arrayChangedCallback: arrayChangedCallback,
                classNames: classNames,
                navigateToItemCallback: navigateToItem,
                sectionElement: sectionElement,
                spec: newSpec,
            });

            array.splice(indexToInsertBefore, 0, newSpec);
            sectionElement.insertBefore(sectionItemElement, elementToInsertBefore);

            arrayChangedCallback();
        }

        var insertCommand = document.createElement("div");
        insertCommand.className = "command insert";
        insertCommand.textContent = "Insert";
        insertCommand.addEventListener("click", insertNew);
        item.commandsElement.appendChild(insertCommand);

        /* closure */
        function insertNew() {
            requestClassName()
                .then(insert)
                .catch(Colby.displayAndReportError);

            function requestClassName() {
                return CBArrayEditor.requestModelClassName({
                    classNames: classNames,
                    navigateToItemCallback: navigateToItem,
                });
            }

            function insert(className) {
                var newSpec = {
                    className: className,
                };

                var indexToInsertBefore = array.indexOf(spec);
                var elementToInsertBefore = sectionElement.children.item(indexToInsertBefore);
                var sectionItemElement = CBArrayEditor.createSectionItemElement2({
                    array: array,
                    arrayChangedCallback: arrayChangedCallback,
                    classNames: classNames,
                    navigateToItemCallback: navigateToItem,
                    sectionElement: sectionElement,
                    spec: newSpec,
                });

                array.splice(indexToInsertBefore, 0, newSpec);
                sectionElement.insertBefore(sectionItemElement, elementToInsertBefore);

                arrayChangedCallback();
            }
        }

        return item.element;
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param function args.classNames
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     * @param object args.specToInsertBefore
     * @param object spec
     *
     * @return  undefined
     */
    insert: function (args, spec) {
        var indexToInsertBefore = args.array.indexOf(args.specToInsertBefore);
        var elementToInsertBefore = args.sectionElement.children.item(indexToInsertBefore);
        var sectionItemElement = CBArrayEditor.createSectionItemElement2({
            array: args.array,
            arrayChangedCallback: args.arrayChangedCallback,
            classNames: args.classNames,
            navigateToItemCallback: args.navigateToItemCallback,
            sectionElement: args.sectionElement,
            spec: spec,
        });

        args.array.splice(indexToInsertBefore, 0, spec);
        args.sectionElement.insertBefore(sectionItemElement, elementToInsertBefore);

        args.arrayChangedCallback();
    },

    /**
     * @param [string] args.classNames
     * @param function args.navigateToItemCallback
     *
     * @return Promise -> string
     */
    requestModelClassName: function (args) {
        return new Promise(function (resolve, reject) {
            if (args.classNames.length === 1) {
                resolve(args.classNames[0]);
                return;
            }

            var options = args.classNames.map(function (className) {
                return {
                    title: className,
                    value: className,
                };
            });

            CBUISelector.showSelector({
                callback: resolve,
                navigateToItemCallback: args.navigateToItemCallback,
                options: options,
                selectedValue: undefined,
                title: "Select a View",
            });
        });
    },
};
