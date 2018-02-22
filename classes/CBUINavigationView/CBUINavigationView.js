"use strict";
/* jshint strict: global */
/* exported CBUINavigationView */
/* global
    CBUI */

/**
 * A navigator is a user interface control that navigates between panels of user
 * interface, each panel contained in an element, to enable responsive user
 * interfaces. A navigator is composed of a navigation bar and a content area.
 * Some user interface controls, such as CBUISelector, will not work unless they
 * are presented inside a navigator.
 *
 * The only way to change the content of a navigator is to navigate to a panel
 * which is contained in an element. After creating a navigator, create an
 * element containing your user interface and the pass it to the navigate()
 * function.
 *
 * Adding content directly to the navigator element will break the navigator.
 */
var CBUINavigationView = {

    /**
     * @param object item
     *
     *      {
     *          element: Element
     *
     *              The element being navigated to.
     *
     *          left: string
     *
     *              String representing the element being navigated from. This
     *              is the "navigate back" text.
     *
     *          rightElements: [Element]
     *
     *              The elements are usually created using the
     *              CBUI.createHeaderButtonItem() function.
     *
     *          title: string
     *
     *              A title for the element being navigated to.
     *      }
     *
     * @return Element
     */
    containerFromItem: function (item) {
        var leftElements, titleElement;
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
            rightElements: item.rightElements,
        });

        container.appendChild(header);
        container.appendChild(item.element);

        return container;
    },

    /**
     * @param object? args
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
     *          element: Element (readonly)
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

        if (args !== undefined && args.rootItem !== undefined) {
            navigateToItemCallback(args.rootItem);
        }

        return {
            get element() {
                return element;
            },
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
     *          left: string
     *          element: Element
     *          rightElements: [Elements]
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
            rightElements: item.rightElements,
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
