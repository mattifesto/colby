"use strict";

var CBUINavigationView = {

    /**
     * @return Element
     */
    containerFromItem : function (item) {
        var container = document.createElement("div");
        container.className = "container";
        container.appendChild(item.element);
        return container;
    },

    /**
     * @param function args.defaultSpecChangedCallback (deprecated)
     * @param object args.rootItem
     *
     * @return {
     *  Element element,
     *  function navigateToElementCallback,
     *  function navigateToSpecCallback, (deprecated)
     * }
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUINavigationView";
        var state = {
            element : element,
            items : [],
        };

        var navigateToItemCallback = CBUINavigationView.navigateToItem.bind(undefined, state);
        var navigateToSpecCallback = CBUINavigationView.navigateToSpec.bind(undefined, {
            defaultSpecChangedCallback : args.defaultSpecChangedCallback,
            navigateToItemCallback : navigateToItemCallback,
        });

        window.addEventListener("popstate", CBUINavigationView.handlePopState.bind(undefined, state));

        navigateToItemCallback(args.rootItem);

        return {
            element : element,
            navigateToItemCallback : navigateToItemCallback,
            navigateToSpecCallback : navigateToSpecCallback,
        };
    },

    /**
     * @param object state
     *
     * @return undefined
     */
    handlePopState : function (state, event) {
        if (state.items.length < 2) { return; }

        /* var from = */ state.items.pop();
        var newContainerElement = state.items[state.items.length - 1].element;

        state.element.textContent = null;
        state.element.appendChild(newContainerElement);
    },

    /**
     * @param object state
     * @param Element item.element
     * @param string item.title
     *
     * @return undefined
     */
    navigateToItem : function (state, item) {
        item = {
            element : item.element,
            title : item.title,
        };

        /* var from = state.elements[state.elements.length - 1]; */
        item.container = CBUINavigationView.containerFromItem(item);

        state.items.push(item);

        state.element.textContent = null;
        state.element.appendChild(item.container);
    },

    /**
     * @deprecated use CBUINavigationView.navigateToElement
     *
     * @param function args.defaultSpecChangedCallback
     * @param function args.navigateToItemCallback
     * @param object spec
     *
     * @return undefined
     */
    navigateToSpec : function (args, spec) {
        var element = document.createElement("div");
        var navigateToSpecCallback = CBUINavigationView.navigateToSpec.bind(undefined, {
            defaultSpecChangedCallback : args.defaultSpecChangedCallback,
            navigateToItemCallback : args.navigateToItemCallback,
        });
        var editor = CBUISpecEditor.create({
            navigateCallback : navigateToSpecCallback,
            navigateToItemCallback : args.navigateToItemCallback,
            spec : spec,
            specChangedCallback : args.defaultSpecChangedCallback,
        });

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(editor.element);
        element.appendChild(CBUI.createHalfSpace());

        args.navigateToItemCallback.call(undefined, {
            element : element,
            title : "thang",
        });

        history.pushState(undefined, undefined);
    },
};
