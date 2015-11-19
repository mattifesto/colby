"use strict";

var CBArrayEditorFactory = {

    /**
     * @param [Object] array
     * @param function arrayChangedCallback
     * @param [string] classNames
     * @param Element sectionElement
     * @param Object spec
     *
     * @return  undefined
     */
    append : function (args) {
        var element = CBArrayEditorFactory.createSectionItemElement({
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
     * @param Element navigationContainer
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
                navigationContainer : args.navigationContainer,
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

        edit.addEventListener("click", CBArrayEditorFactory.navigate.bind(undefined, {
            element : editor,
        }));

        element.appendChild(edit);
        return element;
    },

    /**
     * @param Element element
     *
     * @return undefined
     */
    navigate : function (args) {
        document.body.textContent = null
        document.body.appendChild(args.element);
    },
};

(function() {
    var link    = document.createElement("link");
    link.rel    = "stylesheet";
    link.href   = "/colby/javascript/CBArrayEditor.css";

    document.head.appendChild(link);
})();
