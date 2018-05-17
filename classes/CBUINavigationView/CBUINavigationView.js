"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUINavigationView */
/* global
    CBUI,
*/

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
     * The context property is set to the API object returned by
     * CBUINavigationView.create() the first time the function is called. The
     * function will throw an error if called a second time.
     *
     * History:
     * Originally this class was created with the idea that multiple navigation
     * views could be created on a single page. The navigation view API object
     * was passed around from function to function. This pattern of a resource
     * being passed from function to function becomes awkward quickly.
     *
     * In actual use, there is only one CBUINavigationView created per page so
     * in an effort to greatly simplify code, support for multiple navigation
     * views has been removed. It can be supported, but there will have to be a
     * good, real world, use case example to be used to properly design the
     * programming model.
     *
     * Note: the concept of multiple navigation areas on a single page is
     * awkward from an end user standpoint. Before enabling this functionality a
     * case will have to be made as to how it will not be too much context for
     * the user to be able to comfortably work with.
     */
    context: undefined,

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
     *          rootItem: object? (deprecated)
     *
     *              Specifying a rootItem argument is exactly the same as
     *              calling this function followed by a call to navigate().
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *          navigate: function
     *          navigateToItemCallback: function (deprecated)
     *      }
     */
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUINavigationView";
        var state = {
            element: element,
            items: [],
        };

        window.addEventListener("popstate", CBUINavigationView.handlePopState.bind(undefined, state));

        if (args !== undefined && args.rootItem !== undefined) {
            navigate(args.rootItem);
        }

        let api = {
            get element() {
                return element;
            },

            navigate: navigate,
            navigateToItemCallback: navigate, /* deprecated */
        };

        if (CBUINavigationView.context !== undefined) {
            throw new Error("There is only one CBUINavigationView allowed per page.");
        } else {
            CBUINavigationView.context = api;
        }

        return api;

        /**
         * closure
         *
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
        function navigate(item) {
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
        }
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
};
