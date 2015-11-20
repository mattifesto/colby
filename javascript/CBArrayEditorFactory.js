"use strict";

var CBArrayEditorFactory = {

    /**
     * @param [Object] array
     * @param function arrayChangedCallback
     * @param [string] classNames
     * @param function navigateCallback
     * @param Element sectionElement
     * @param Object spec
     *
     * @return  undefined
     */
    append : function (args) {
        var element = CBArrayEditorFactory.createSectionItemElement({
            navigateCallback : args.navigateCallback,
            spec : args.spec,
        });

        args.array.push(args.spec);
        args.sectionElement.insertBefore(element, args.sectionElement.lastElementChild);

        args.arrayChangedCallback.call();
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
            var element = CBArrayEditorFactory.createSectionItemElement({
                array : args.array,
                arrayChangedCallback : args.arrayChangedCallback,
                navigateCallback : args.navigateCallback,
                sectionElement : section,
                spec : spec,
            });

            section.appendChild(element);
        });

        section.appendChild(CBArrayEditorFactory.createMenu({
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

        item.addEventListener("click", CBArrayEditorFactory.append.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : args.sectionElement,
            spec : { className : "CBMenuItem" },
        }));

        element.appendChild(item);

        item = document.createElement("div");
        item.textContent = "arrange";

        item.addEventListener("click", CBArrayEditorFactory.setEditorMode.bind(undefined, {
            mode : "arrange",
            sectionElement : args.sectionElement,
        }));

        element.appendChild(item);

        item = document.createElement("div");
        item.textContent = "edit";

        item.addEventListener("click", CBArrayEditorFactory.setEditorMode.bind(undefined, {
            mode : "edit",
            sectionElement : args.sectionElement,
        }));

        element.appendChild(item);

        item = document.createElement("div");
        item.textContent = "insert";

        item.addEventListener("click", CBArrayEditorFactory.setEditorMode.bind(undefined, {
            mode : "insert",
            sectionElement : args.sectionElement,
        }));

        element.appendChild(item);

        return element;
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
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
        content.textContent = args.spec.className;

        content.addEventListener("click", args.navigateCallback.bind(undefined, {
            spec : args.spec,
        }));

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

        action.addEventListener("click", CBArrayEditorFactory.handleDeleteWasClicked.bind(undefined, {
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
     * @param string mode
     * @param Element sectionElement
     *
     * @return undefined
     */
    setEditorMode : function (args) {
        args.sectionElement.className = "CBUISection " + args.mode;
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
