"using strict";

var CBPageEditor2 = {

    /**
     * @param {Object} spec
     *
     * @return {Element}
     */
    createEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBPageEditor2";

        var specEditor = CBPageEditor2.createSpecEditor({
            containerElement : element,
            parentEditorElement : undefined,
            parentSpec : undefined,
            spec : args.spec
        });

        element.appendChild(specEditor);

        return element;
    },

    /**
     * @param {Element} containerElement
     * @param {Element} parentEditorElement
     * @param {Object} parentSpec
     * @param {Object} spec
     *
     * @return {Element}
     */
    createLinkElement : function(args) {
        var element = document.createElement("div");
        element.className = "SpecLink";
        element.textContent = args.spec.className;

        element.addEventListener("click", CBPageEditor2.navigateUp.bind(undefined, {
            containerElement : args.containerElement,
            parentEditorElement : args.parentEditorElement,
            parentSpec : args.parentSpec,
            spec : args.spec
        }));

        return element;
    },

    /**
     * @param {Element} containerElement
     * @param {Element} parentEditorElement
     * @param {Object} parentSpec
     * @param {Object} spec
     *
     * @return
     */
    createSpecEditor : function(args) {
        var element = document.createElement("div");
        element.className= "SpecEditor";
        var header = document.createElement("header");
        var leftnav = document.createElement("nav");
        leftnav.textContent = args.parentSpec ? 'Back' : '';
        var rightnav = document.createElement("nav");
        var h1 = document.createElement("h1");
        h1.textContent = args.parentSpec ? args.spec.className : 'Page';

        if (args.parentEditorElement) {
            leftnav.addEventListener("click", CBPageEditor2.navigateDown.bind(undefined, {
                containerElement : args.containerElement,
                parentEditorElement : args.parentEditorElement
            }));
        }

        header.appendChild(leftnav);
        header.appendChild(h1);
        header.appendChild(rightnav);
        element.appendChild(header);

        if (Array.isArray(args.spec.sections)) {
            element.appendChild(CBPageEditor2.createSubviewsNavigation({
                containerElement : args.containerElement,
                parentEditorElement : element,
                parentSpec : args.spec,
                subviews : args.spec.sections
            }));
        }

        if (args.parentSpec) {
            var editorFactory   = window[args.spec.className + "EditorFactory"] || CBEditorWidgetFactory;
            var editor          = editorFactory.createEditor({
                handleSpecChanged : CBPageEditor.requestSave.bind(CBPageEditor),
                spec : args.spec,
            });

            element.appendChild(editor);
        }

        return element;
    },

    /**
     * @param {Element} containerElement
     * @param {Element} parentEditorElement
     * @param {Object} parentSpec
     * @param {Array} subviews
     *
     * @return {Element}
     */
    createSubviewsNavigation : function(args) {
        var element = document.createElement("div");
        element.className = "CBPageEditorSubviewNavigation";

        var container = document.createElement("div");

        var description = document.createElement("div")
        description.className = "CBPageEditorDescription";
        description.textContent = "SUBVIEWS";

        container.appendChild(description);

        var list = document.createElement("div");
        list.className = "list";

        for (var i = 0; i < args.subviews.length; i++) {
            list.appendChild(CBPageEditor2.createLinkElement({
                containerElement : args.containerElement,
                parentEditorElement : args.parentEditorElement,
                parentSpec : args.parentSpec,
                spec : args.subviews[i],
            }));
        }

        container.appendChild(list);
        element.appendChild(container);

        return element;
    },

    /**
     * @param {Element} containerElement
     * @param {Element} parentEditorElement
     *
     * @return undefined
     */
    navigateDown : function(args) {
        args.containerElement.textContent = null;
        args.containerElement.appendChild(args.parentEditorElement);
    },

    /**
     * @param {Element} containerElement
     * @param {Element} parentEditorElement
     * @param {Object} parentSpec
     * @param {Object} spec
     *
     * @return undefined
     */
    navigateUp : function(args) {
        var specEditor = CBPageEditor2.createSpecEditor({
            containerElement : args.containerElement,
            parentEditorElement : args.parentEditorElement,
            parentSpec : args.parentSpec,
            spec : args.spec
        });

        args.containerElement.textContent = null;
        args.containerElement.appendChild(specEditor);
    },
};
