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

        var links = [];

        if (Array.isArray(args.spec.sections)) {
            links = args.spec.sections.map(function(spec) {
                return CBPageEditor2.createLinkElement({
                    containerElement : args.containerElement,
                    parentEditorElement : element,
                    parentSpec : args.spec,
                    spec : spec
                });
            });
        }

        header.appendChild(leftnav);
        header.appendChild(h1);
        header.appendChild(rightnav);
        element.appendChild(header);

        for (var i = 0; i < links.length; i++) {
            element.appendChild(links[i]);
        }

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
