"use strict";

/**
 * @deprecated use CBArrayEditor
 */
var CBArrayEditorFactory;

var CBArrayEditor = CBArrayEditorFactory = {

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param Element args.sectionElement
     * @param Object spec
     *
     * @return  undefined
     */
    append : function (args, spec) {
        var element = CBArrayEditor.createSectionItemElement({
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : args.sectionElement,
            spec : spec,
        });

        args.array.push(spec);
        args.sectionElement.insertBefore(element, args.sectionElement.lastElementChild);

        args.arrayChangedCallback.call();
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param Element args.sectionElement
     *
     * @return  undefined
     */
    appendSelectedModel : function (args) {
        var requestModelClassName = CBArrayEditor.requestModelClassName;
        var requestArgs = { classNames : args.classNames, };
        var classNameToModel = CBArrayEditor.classNameToModel;
        var appendModel = CBArrayEditor.append.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : args.sectionElement,
        });

        requestModelClassName(requestArgs).then(classNameToModel).then(appendModel);
    },

    /**
     * @param string className
     *
     * @return Object
     */
    classNameToModel : function(className) {
        return {
            className : className,
        };
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var element = document.createElement("div");
        element.className = "CBArrayEditor";

        var section = document.createElement("div");
        section.className = "CBUISection";

        args.array.forEach(function (spec) {
            var element = CBArrayEditor.createSectionItemElement({
                array : args.array,
                arrayChangedCallback : args.arrayChangedCallback,
                classNames : args.classNames,
                navigateCallback : args.navigateCallback,
                sectionElement : section,
                spec : spec,
            });

            section.appendChild(element);
        });

        section.appendChild(CBArrayEditor.createMenu({
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : section,
        }));

        element.appendChild(section);

        return element;
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param Element args.sectionElement
     *
     * @return Element
     */
    createMenu : function (args) {
        var item;
        var element = document.createElement("div");
        element.className = "CBUISectionItem menu";

        item = document.createElement("div");
        item.textContent = "append";

        item.addEventListener("click", CBArrayEditor.appendSelectedModel.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : args.sectionElement,
        }));

        element.appendChild(item);

        item = document.createElement("div");
        item.textContent = "arrange";

        item.addEventListener("click", CBArrayEditor.setEditorMode.bind(undefined, {
            mode : "arrange",
            sectionElement : args.sectionElement,
        }));

        element.appendChild(item);

        item = document.createElement("div");
        item.textContent = "edit";

        item.addEventListener("click", CBArrayEditor.setEditorMode.bind(undefined, {
            mode : "edit",
            sectionElement : args.sectionElement,
        }));

        element.appendChild(item);

        item = document.createElement("div");
        item.textContent = "insert";

        item.addEventListener("click", CBArrayEditor.setEditorMode.bind(undefined, {
            mode : "insert",
            sectionElement : args.sectionElement,
        }));

        element.appendChild(item);

        return element;
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param Element args.sectionElement
     * @param Object args.spec
     *
     * @return Element
     */
    createSectionItemElement : function (args) {
        var action;
        var element = document.createElement("div");
        element.className = "CBUISectionItem";

        var content = document.createElement("div");
        content.className = "content";

        content.addEventListener("click", args.navigateCallback.bind(undefined, args.spec));

        var title = document.createElement("div");
        title.className = "title";
        title.textContent = args.spec.className;

        var description = document.createElement("div");
        description.className = "description";
        description.textContent = CBArrayEditor.specToDescription(args.spec) || "";

        content.appendChild(title);
        content.appendChild(description);
        element.appendChild(content);

        // arrange

        action = document.createElement("div");
        action.className = "action arrange up";
        action.textContent = "up";

        element.appendChild(action);

        action = document.createElement("div");
        action.className = "action arrange down";
        action.textContent = "down";

        element.appendChild(action);

        // edit

        action = document.createElement("div");
        action.className = "action edit cut";
        action.textContent = "x";

        action.addEventListener("click", CBArrayEditor.handleDeleteWasClicked.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));

        element.appendChild(action);

        action = document.createElement("div");
        action.className = "action edit copy";
        action.textContent = "c";

        element.appendChild(action);

        action = document.createElement("div");
        action.className = "action edit paste";
        action.textContent = "p";

        element.appendChild(action);

        // insert

        action = document.createElement("div");
        action.className = "action insert";
        action.textContent = "+";

        action.addEventListener("click", CBArrayEditor.insertSelectedModel.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : args.sectionElement,
            specToInsertBefore : args.spec,
        }));

        element.appendChild(action);

        return element;
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
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param Element args.sectionElement
     * @param Object args.spec
     *
     * @return undefined
     */
    handleDeleteWasClicked : function (args) {
        if (confirm("Are you sure you want to remove this item?")) {
            var index = args.array.indexOf(args.spec);
            var itemElement = args.sectionElement.children.item(index);

            args.array.splice(index, 1); // remove at index
            args.sectionElement.removeChild(itemElement);

            args.arrayChangedCallback.call();
        }
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param function args.classNames
     * @param function args.navigateCallback
     * @param Element args.sectionElement
     * @param Object args.specToInsertBefore
     * @param Object spec
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
            sectionElement : args.sectionElement,
            spec : spec,
        });

        args.array.splice(indexToInsertBefore, 0, spec);
        args.sectionElement.insertBefore(sectionItemElement, elementToInsertBefore);

        args.arrayChangedCallback.call();
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param [string] args.classNames
     * @param function args.navigateCallback
     * @param Element args.sectionElement
     * @param Object args.specToInsertBefore
     *
     * @return  undefined
     */
    insertSelectedModel : function (args) {
        var requestModelClassName = CBArrayEditor.requestModelClassName;
        var requestArgs = { classNames : args.classNames, };
        var classNameToModel = CBArrayEditor.classNameToModel;
        var insertModel = CBArrayEditor.insert.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : args.sectionElement,
            specToInsertBefore : args.specToInsertBefore,
        });

        requestModelClassName(requestArgs).then(classNameToModel).then(insertModel);
    },

    /*
     * @param [string] args.classNames
     *
     * @return Promise -> string
     */
    requestModelClassName : function(args) {
        return new Promise(function (resolve, reject) {
            if (args.classNames.length === 1) {
                resolve(args.classNames[0]);
                return;
            }

            var element = document.createElement("div");
            element.className = "CBArrayEditorModelSelector CBUIRoot";

            var title = document.createElement("div");
            title.textContent = "Select a Model Class";

            var cancel = document.createElement("div");
            cancel.className = "CBUIHeaderAction";
            cancel.textContent = "Cancel";

            cancel.addEventListener("click", function () {
                document.body.removeChild(element);
            });

            element.appendChild(CBUI.createHeader({
                centerElement : title,
                rightElement : cancel,
            }));

            element.appendChild(CBUI.createHalfSpace());

            var section = CBUI.createSection();

            args.classNames.forEach(function (className) {
                var item = CBUI.createSectionItem();
                item.classList.add("item");
                item.textContent = className;

                item.addEventListener("click", function () {
                    document.body.removeChild(element);
                    resolve(className);
                });

                section.appendChild(item);
            });

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());

            document.body.appendChild(element);
        });
    },

    /**
     * @param string mode
     * @param Element sectionElement
     *
     * @return undefined
     */
    setEditorMode : function (args) {
        var e = args.sectionElement;

        if (e.classList.contains(args.mode)) {
            e.classList.toggle(args.mode);
        } else {
            e.className = "CBUISection " + args.mode;
        }
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
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
