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

        var item = document.createElement("div");
        item.className = "CBUISectionItem";
        item.textContent = "Add";

        item.addEventListener("click", CBArrayEditorFactory.append.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            classNames : args.classNames,
            navigateCallback : args.navigateCallback,
            sectionElement : section,
            spec : { className : "CBMenuItem" },
        }));

        section.appendChild(item);
        element.appendChild(section);

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
        var element = document.createElement("div");
        element.className = "CBUISectionItem";
        element.textContent = args.spec.className;

        var edit = document.createElement("div");
        edit.textContent = "edit";

        edit.addEventListener("click", args.navigateCallback.bind(undefined, {
            spec : args.spec,
        }));

        element.appendChild(edit);

        var action = document.createElement("div");
        action.textContent = "delete";

        action.addEventListener("click", CBArrayEditorFactory.handleDelete.bind(undefined, {
            array : args.array,
            arrayChangedCallback : args.arrayChangedCallback,
            sectionElement : args.sectionElement,
            spec : args.spec,
        }));

        element.appendChild(action);

        return element;
    },

    /**
     * @param [Object] args.array
     * @param function args.arrayChangedCallback
     * @param Element args.sectionElement
     * @param Object args.spec
     *
     * @return undefined
     */
    handleDelete : function (args) {
        if (confirm("Are you sure you want to remove this item?")) {
            var index = args.array.indexOf(args.spec);
            var itemElement = args.sectionElement.children.item(index);

            args.array.splice(index, 1); // remove at index
            args.sectionElement.removeChild(itemElement);

            args.arrayChangedCallback.call();
        }
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
