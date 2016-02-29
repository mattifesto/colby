"use strict";

/**
 * @deprecated use CBArrayEditor
 */
var CBArrayEditorFactory;

var CBArrayEditor = CBArrayEditorFactory = {

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
        var element = CBArrayEditor.createSectionItemElement({
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            spec : spec,
        });

        args.array.push(spec);
        args.sectionElement.insertBefore(element, args.sectionElement.lastElementChild);

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
    classNameToModel : function(className) {
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
            var element = CBArrayEditor.createSectionItemElement({
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
            labelText : "Append...",
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
    createSectionItemElement : function (args) {
        var action;
        var element = CBUI.createSectionItem();
        element.classList.add("item");

        var content = document.createElement("div");
        content.className = "content";
        var title = document.createElement("div");
        title.className = "title";
        title.textContent = args.spec.className;
        var description = document.createElement("div");
        description.className = "description";

        var arrayElementChangedCallback = CBArrayEditor.handleArrayElementChanged.bind(undefined, {
            descriptionElement : description,
            spec : args.spec,
            specChangedCallback : args.arrayChangedCallback,
        });

        arrayElementChangedCallback();

        content.appendChild(title);
        content.appendChild(description);
        element.appendChild(content);

        var editArrayElementRequestedCallback = CBArrayEditor.handleEditArrayElementRequested.bind(undefined, {
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            spec : args.spec,
            specChangedCallback : arrayElementChangedCallback,
        });

        content.addEventListener("click", editArrayElementRequestedCallback);

        // arrange
        action = document.createElement("div");
        action.className = "action arrange up";
        action.textContent = "up";
        action.addEventListener("click", CBArrayEditor.handleMoveUpWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));
        element.appendChild(action);

        action = document.createElement("div");
        action.className = "action arrange down optional";
        action.textContent = "down";
        action.addEventListener("click", CBArrayEditor.handleMoveDownWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));
        element.appendChild(action);

        // edit
        action = document.createElement("div");
        action.className = "action edit cut";
        action.textContent = "cut";
        action.addEventListener("click", CBArrayEditor.handleCutWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));
        element.appendChild(action);

        action = document.createElement("div");
        action.className = "action edit copy optional";
        action.textContent = "copy";
        action.addEventListener("click", CBArrayEditor.handleCopyWasClicked.bind(undefined, {
            spec : args.spec,
        }));
        element.appendChild(action);

        action = document.createElement("div");
        action.className = "action edit paste";
        action.textContent = "paste";
        action.addEventListener("click", CBArrayEditor.handlePasteWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            specToInsertBefore : args.spec,
        }));
        element.appendChild(action);

        // insert
        action = document.createElement("div");
        action.className = "action insert";
        action.textContent = "insert";
        action.addEventListener("click", CBArrayEditor.insertSelectedModel.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            sectionElement : args.sectionElement,
            specToInsertBefore : args.spec,
        }));
        element.appendChild(action);

        /* toggle */
        action = document.createElement("div");
        action.className = "toggle";
        action.textContent = "<";
        action.addEventListener("click", CBArrayEditor.toggleActions.bind(undefined, {
            toggleButtonElement : action,
            sectionItemElement : element,
        }));
        element.appendChild(action);

        return element;
    },

    /**
     * @param Element args.descriptionElement
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     */
    handleArrayElementChanged : function (args) {
        var nonBreakingSpace = "\u00A0";
        args.descriptionElement.textContent = CBArrayEditor.specToDescription(args.spec) || nonBreakingSpace;
        args.specChangedCallback.call();
    },

    /**
     * @param Element args.element
     * @param Element args.sectionElement
     *
     * @return undefined
     */
    handleContentWasClicked : function (args) {
        var elements = args.sectionElement.querySelectorAll(".CBUISectionItem.selected");

        for (var i = 0; i < elements.length; i++) {
            elements[i].classList.remove("selected");
        }

        args.element.classList.add("selected");
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
     * @param function args.navigateCallback (deprecated)
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    handleEditArrayElementRequested : function (args) {
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
        var sectionItemElement = CBArrayEditor.createSectionItemElement({
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
    requestModelClassName : function(args) {
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
     * @param object? spec
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        if (spec === undefined) { return undefined; }

        var editor = window[spec.className + "Editor"];

        if (editor !== undefined && typeof editor.specToDescription === "function") {
            return editor.specToDescription.call(undefined, spec);
        } else {
            return spec.title;
        }
    },

    /**
     * @param Element args.sectionItemElement
     * @param Element args.toggleButtonElement
     *
     * @return undefined
     */
    toggleActions : function (args) {
        if (args.sectionItemElement.classList.contains("show-actions")) {
            args.sectionItemElement.classList.remove("show-actions");
            args.toggleButtonElement.textContent = "<";
        } else {
            args.sectionItemElement.classList.add("show-actions");
            args.toggleButtonElement.textContent = ">";
        }
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
