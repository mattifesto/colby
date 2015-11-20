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
     * @param [Object] array
     * @param function arrayChangedCallback
     * @param [string] classNames
     * @param function navigateCallback
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
                navigateCallback : args.navigateCallback,
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
     * @param Object spec
     *
     * @return Element
     */
    createSectionItemElement : function (args) {
        var element = document.createElement("div");
        element.className = "CBUISectionItem";
        element.textContent = args.spec.className;

        var edit = document.createElement("div");
        edit.textContent = "edit";

        var editorFactory   = window[args.spec.className + "EditorFactory"] || CBEditorWidgetFactory;
        var editor          = editorFactory.createEditor(args);

        edit.addEventListener("click", args.navigateCallback.bind(undefined, {
            spec : args.spec,
        }));

        element.appendChild(edit);
        return element;
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
