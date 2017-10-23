"use strict";
/* jshint strict: global */
/* exported CBUINavigationView */
/* global
    CBUI */

var CBUINavigationView = {

    /**
     * @param object item
     *
     * @return Element
     */
    containerFromItem: function (item) {
        var leftElements, rightElements, titleElement;
        var container = document.createElement("div");
        container.className = "container";

        if (typeof item.title === "string") {
            titleElement = CBUI.createHeaderTitle({
                text: item.title,
            });
        }

        if (typeof item.left === "string") {
            leftElements = [CBUI.createHeaderButtonItem({
                callback: window.history.back.bind(window.history),
                text: "< " + item.left,
            })];
        }

        var header = CBUI.createHeader({
            centerElement: titleElement,
            leftElements: leftElements,
            rightElements: rightElements,
        });

        container.appendChild(header);
        container.appendChild(item.element);

        return container;
    },

    /**
     * @param object args
     *
     *      {
     *          rootItem: object?
     *
     *              Specifying a rootItem argument is exactly the same as
     *              calling this function followed by a call to
     *              navigateToItemCallback().
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *          navigateToItemCallback: function
     *      }
     */
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUINavigationView";
        var state = {
            element: element,
            items: [],
        };

        var navigateToItemCallback = CBUINavigationView.navigateToItem.bind(undefined, state);

        window.addEventListener("popstate", CBUINavigationView.handlePopState.bind(undefined, state));

        if (args.rootItem !== undefined) {
            navigateToItemCallback(args.rootItem);
        }

        return {
            element: element,
            navigateToItemCallback: navigateToItemCallback,
        };
    },

    /**
     * @param object state
     *
     * @return undefined
     */
    handlePopState: function (state, event) {
        if (state.items.length < 2) { return; }

        /* var from = */ state.items.pop();
        var newContainerElement = state.items[state.items.length - 1].container;

        state.element.textContent = null;
        state.element.appendChild(newContainerElement);
    },

    /**
     * @param object state
     * @param object item
     *
     *      {
     *          element: Element
     *          title: string
     *      }
     *
     * @return undefined
     */
    navigateToItem: function (state, item) {
        var fromItem;
        var toItem = {
            element: item.element,
            left: item.left,
            right: item.right,
            title: item.title,
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

        window.scrollTo(0, 0);
    },
};
