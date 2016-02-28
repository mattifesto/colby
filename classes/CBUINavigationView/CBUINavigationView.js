"use strict";

var CBUINavigationView = {

    /**
     * @return Element
     */
    containerFromItem : function (item) {
        var leftElements, rightElements, titleElement;
        var container = document.createElement("div");
        container.className = "container";

        if (typeof item.title === "string") {
            titleElement = CBUI.createHeaderTitle({
                text : item.title,
            });
        }

        if (typeof item.left === "string") {
            leftElements = [CBUI.createHeaderButtonItem({
                callback : window.history.back.bind(window.history),
                text : "< " + item.left,
            })];
        }

        var header = CBUI.createHeader({
            centerElement : titleElement,
            leftElements : leftElements,
            rightElements : rightElements,
        });

        container.appendChild(header);
        container.appendChild(item.element);

        return container;
    },

    /**
     * @param function args.defaultSpecChangedCallback (deprecated)
     * @param object? args.rootItem
     *  Specifying a rootItem argument is exactly the same as calling this
     *  function followed by a call to navigateToItemCallback().
     *
     * @return {
     *  Element element,
     *  function navigateToItemCallback,
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
        var navigateToSpecCallback = CBUINavigationView.navigateToSpec.bind(undefined,
            args.defaultSpecChangedCallback, navigateToItemCallback);

        window.addEventListener("popstate", CBUINavigationView.handlePopState.bind(undefined, state));

        if (args.rootItem !== undefined) {
            navigateToItemCallback(args.rootItem);
        }

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
        var newContainerElement = state.items[state.items.length - 1].container;

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
        var fromItem;
        var toItem = {
            element : item.element,
            left : item.left,
            right : item.right,
            title : item.title,
        };

        if (state.items.length > 0) {
            fromItem = state.items[state.items.length - 1];

            if (toItem.left === undefined) {
                toItem.left = fromItem.title;
            }
        }

        /* var from = state.elements[state.elements.length - 1]; */
        toItem.container = CBUINavigationView.containerFromItem(toItem);

        state.items.push(toItem);

        state.element.textContent = null;
        state.element.appendChild(toItem.container);

        if (state.items.length > 1) {
            history.pushState(undefined, undefined);
        }
    },

    /**
     * @deprecated use CBUINavigationView.navigateToItem
     *
     * @param function args.defaultSpecChangedCallback
     * @param function args.navigateToItemCallback
     * @param object spec
     *
     * @return undefined
     */
    navigateToSpec : function (defaultSpecChangedCallback, navigateToItemCallback, spec) {
        var element = document.createElement("div");
        var navigateToSpecCallback = CBUINavigationView.navigateToSpec.bind(undefined,
            defaultSpecChangedCallback, navigateToItemCallback);
        var editor = CBUISpecEditor.create({
            navigateCallback : navigateToSpecCallback,
            navigateToItemCallback : navigateToItemCallback,
            spec : spec,
            specChangedCallback : defaultSpecChangedCallback,
        });

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(editor.element);
        element.appendChild(CBUI.createHalfSpace());

        navigateToItemCallback({
            element : element,
            title : spec.title || spec.className || "Unknown",
        });
    },
};
