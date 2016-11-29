"use strict"; /* jshint strict: global */
/* globals
    CBUI,
    CBUIActionLink,
    CBUISelector,
    CBUISpec,
    CBUISpecEditor,
    Colby,
    Promise */

var CBArrayEditor = {

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     * @param object spec
     *
     * @return  undefined
     */
    append : function (args, spec) {
        var element = CBArrayEditor.createSectionItemElement2({
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            spec : spec,
        });

        args.array.push(spec);
        args.sectionElement.insertBefore(element, args.sectionElement.lastElementChild.previousSibling);

        args.arrayChangedCallback.call();
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     *
     * @return  undefined
     */
    appendFromClipboardWasClicked : function (args) {
        var specAsJSON = localStorage.getItem("specClipboard");

        if (specAsJSON === null) { return; }

        var spec = JSON.parse(specAsJSON);

        CBArrayEditor.append(args, spec);
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     *
     * @return  undefined
     */
    appendSelectedModel : function (args) {
        var requestModelClassName = CBArrayEditor.requestModelClassName;
        var requestArgs = {
            classNames : args.classNames,
            navigateToItemCallback : args.navigateToItemCallback,
        };
        var classNameToModel = CBArrayEditor.classNameToModel;
        var appendModel = CBArrayEditor.append.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
        });

        requestModelClassName(requestArgs).then(classNameToModel).then(appendModel);
    },

    /**
     * @param string className
     *
     * @return object
     */
    classNameToModel: function (className) {
        return {
            className : className,
        };
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBArrayEditor";

        section = document.createElement("div");
        section.className = "CBUISection";

        args.array.forEach(function (spec) {
            var element = CBArrayEditor.createSectionItemElement2({
                array : args.array,
                arrayChangedCallback : args.arrayChangedCallback,
                classNames : args.classNames,
                navigateCallback : args.navigateCallback,
                navigateToItemCallback : args.navigateToItemCallback,
                sectionElement : section,
                spec : spec,
            });

            section.appendChild(element);
        });

        /* append */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback : CBArrayEditor.appendSelectedModel.bind(undefined, {
                array : args.array,
                arrayChangedCallback : args.arrayChangedCallback,
                classNames : args.classNames,
                navigateCallback : args.navigateCallback,
                navigateToItemCallback : args.navigateToItemCallback,
                sectionElement : section,
            }),
            labelText : "Append New...",
        }).element);
        section.appendChild(item);

        /* append from clipboard */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback : CBArrayEditor.appendFromClipboardWasClicked.bind(undefined, {
                array : args.array,
                arrayChangedCallback : args.arrayChangedCallback,
                classNames : args.classNames,
                navigateCallback : args.navigateCallback,
                navigateToItemCallback : args.navigateToItemCallback,
                sectionElement : section,
            }),
            labelText : "Append From Clipboard...",
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     * @param object args.spec
     *
     * @return Element
     */
    createSectionItemElement2 : function (args) {
        var item = CBUI.createSectionItem2();

        var typeElement = document.createElement("div");
        typeElement.className = "type";
        typeElement.textContent = args.spec.className;
        item.titleElement.appendChild(typeElement);

        var descriptionElement = document.createElement("div");
        descriptionElement.className = "description";
        item.titleElement.appendChild(descriptionElement);

        /* specChangedCallback */

        var descriptionChangedCallback = CBArrayEditor.updateSpecDescriptionElement.bind(undefined, {
            descriptionElement: descriptionElement,
            spec: args.spec,
        });
        descriptionChangedCallback();

        var specChangedCallback = Colby.call.bind(undefined, [
            descriptionChangedCallback,
            args.arrayChangedCallback,
        ]);

        /* edit */

        var editSpecCallback = CBArrayEditor.editSpec.bind(undefined, {
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            spec : args.spec,
            specChangedCallback : specChangedCallback,
        });

        item.titleElement.addEventListener("click", editSpecCallback);

        /* commands */

        var upCommand = document.createElement("div");
        upCommand.className = "command arrange up";
        upCommand.textContent = "Up";
        upCommand.addEventListener("click", CBArrayEditor.handleMoveUpWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));
        item.commandsElement.appendChild(upCommand);

        var downCommand = document.createElement("div");
        downCommand.className = "command arrange down optional";
        downCommand.textContent = "Down";
        downCommand.addEventListener("click", CBArrayEditor.handleMoveDownWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));
        item.commandsElement.appendChild(downCommand);

        var cutCommand = document.createElement("div");
        cutCommand.className = "command edit cut";
        cutCommand.textContent = "Cut";
        cutCommand.addEventListener("click", CBArrayEditor.handleCutWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));
        item.commandsElement.appendChild(cutCommand);

        var copyCommand = document.createElement("div");
        copyCommand.className = "command edit copy optional";
        copyCommand.textContent = "Copy";
        copyCommand.addEventListener("click", CBArrayEditor.handleCopyWasClicked.bind(undefined, {
            spec : args.spec,
        }));
        item.commandsElement.appendChild(copyCommand);

        var pasteCommand = document.createElement("div");
        pasteCommand.className = "command edit paste";
        pasteCommand.textContent = "Paste";
        pasteCommand.addEventListener("click", CBArrayEditor.handlePasteWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            specToInsertBefore : args.spec,
        }));
        item.commandsElement.appendChild(pasteCommand);

        var insertCommand = document.createElement("div");
        insertCommand.className = "command insert";
        insertCommand.textContent = "Insert";
        insertCommand.addEventListener("click", CBArrayEditor.insertSelectedModel.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            specToInsertBefore : args.spec,
        }));
        item.commandsElement.appendChild(insertCommand);

        return item.element;
    },

    /**
     * @param function args.navigateCallback (deprecated)
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    editSpec: function (args) {
        var element = document.createElement("div");
        var editor = CBUISpecEditor.create({
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(editor.element);
        element.appendChild(CBUI.createHalfSpace());

        args.navigateToItemCallback({
            element : element,
            title : args.spec.className || "Unknown",
        });
    },

    /**
     * @param object args.spec
     *
     * @return undefined
     */
    handleCopyWasClicked : function (args) {
        var specAsJSON = JSON.stringify(args.spec);
        localStorage.setItem("specClipboard", specAsJSON);
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param Element args.sectionElement
     * @param object args.spec
     *
     * @return undefined
     */
    handleCutWasClicked : function (args) {
        if (confirm("Are you sure you want to remove this item?")) {
            var index = args.array.indexOf(args.spec);
            var itemElement = args.sectionElement.children.item(index);

            args.array.splice(index, 1); // remove at index
            args.sectionElement.removeChild(itemElement);

            var specAsJSON = JSON.stringify(args.spec);
            localStorage.setItem("specClipboard", specAsJSON);

            args.arrayChangedCallback();
        }
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param Element args.sectionElement
     * @param object args.spec
     *
     * @return undefined
     */
    handleMoveDownWasClicked : function (args) {
        var index = args.array.indexOf(args.spec);

        if (index < (args.array.length - 1)) {
            var itemElement = args.sectionElement.children.item(index);
            var nextItemElement = itemElement.nextSibling;

            args.array.splice(index, 1); // remove at index
            args.array.splice(index + 1, 0, args.spec); // insert after next spec

            args.sectionElement.removeChild(itemElement);
            args.sectionElement.insertBefore(itemElement, nextItemElement.nextSibling);

            args.arrayChangedCallback();
        }
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param Element args.sectionElement
     * @param object args.spec
     *
     * @return undefined
     */
    handleMoveUpWasClicked : function (args) {
        var index = args.array.indexOf(args.spec);

        if (index > 0) {
            var itemElement = args.sectionElement.children.item(index);
            var previousItemElement = itemElement.previousSibling;

            args.array.splice(index, 1); // remove at index
            args.array.splice(index - 1, 0, args.spec); // insert before previous spec

            args.sectionElement.removeChild(itemElement);
            args.sectionElement.insertBefore(itemElement, previousItemElement);

            args.arrayChangedCallback();
        }
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     * @param object args.specToInsertBefore
     *
     * @return  undefined
     */
    handlePasteWasClicked : function (args) {
        var specAsJSON = localStorage.getItem("specClipboard");

        if (specAsJSON === null) { return; }

        var spec = JSON.parse(specAsJSON);

        CBArrayEditor.insert(args, spec);
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param function args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     * @param object args.specToInsertBefore
     * @param object spec
     *
     * @return  undefined
     */
    insert : function (args, spec) {
        var indexToInsertBefore = args.array.indexOf(args.specToInsertBefore);
        var elementToInsertBefore = args.sectionElement.children.item(indexToInsertBefore);
        var sectionItemElement = CBArrayEditor.createSectionItemElement2({
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            spec : spec,
        });

        args.array.splice(indexToInsertBefore, 0, spec);
        args.sectionElement.insertBefore(sectionItemElement, elementToInsertBefore);

        args.arrayChangedCallback();
    },

    /**
     * @param [object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param function args.navigateToItemCallback
     * @param Element args.sectionElement
     * @param object args.specToInsertBefore
     *
     * @return  undefined
     */
    insertSelectedModel : function (args) {
        var requestModelClassName = CBArrayEditor.requestModelClassName;
        var requestArgs = {
            classNames : args.classNames,
            navigateToItemCallback : args.navigateToItemCallback,
        };
        var classNameToModel = CBArrayEditor.classNameToModel;
        var insertModel = CBArrayEditor.insert.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            specToInsertBefore : args.specToInsertBefore,
        });

        requestModelClassName(requestArgs).then(classNameToModel).then(insertModel);
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
                    title : className,
                    value : className,
                };
            });

            CBUISelector.showSelector({
                callback : resolve,
                navigateToItemCallback : args.navigateToItemCallback,
                options : options,
                selectedValue : undefined,
                title : "Select a View",
            });
        });
    },

    /**
     * @param Element args.descriptionElement
     * @param object args.spec
     *
     * @return undefined
     */
    updateSpecDescriptionElement: function (args) {
        var nonBreakingSpace = "\u00A0";
        args.descriptionElement.textContent = CBUISpec.specToDescription(args.spec) || nonBreakingSpace;
    },
};


/**
 * @deprecated use CBArrayEditor
 */
var CBArrayEditorFactory = CBArrayEditor;


(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
