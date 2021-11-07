"use strict";
/* jshint strict: global */
/* exported CBUINavigationView */
/* global
    CBModel,
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
    containerFromItem(
        item
    ) {
        let leftElements;
        let titleElement;

        let container = document.createElement(
            "div"
        );

        container.className = "CBUINavigationView_container container";

        if (
            typeof item.title === "string"
        ) {
            titleElement = CBUI.createHeaderTitle(
                {
                    text: item.title,
                }
            );
        }

        if (
            typeof item.left === "string"
        ) {
            leftElements = [
                CBUI.createHeaderButtonItem(
                    {
                        callback: function () {
                            window.history.back();
                        },
                        text: (
                            "< " +
                            item.left
                        ),
                    }
                )
            ];
        }

        var header = CBUI.createHeader(
            {
                centerElement: titleElement,
                leftElements: leftElements,
                rightElements: item.rightElements,
            }
        );

        container.appendChild(
            header
        );

        container.appendChild(
            item.element
        );

        return container;
    },
    /* containerFromItem() */



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
     *      }
     */
    create(
        args
    ) {
        let element = document.createElement(
            "div"
        );

        element.className = "CBUINavigationView";

        let state = {
            element: element,
            items: [],
        };

        let level;

        window.addEventListener(
            "popstate",
            handlePopState
        );

        if (
            args !== undefined &&
            args.rootItem !== undefined
        ) {
            navigate(
                args.rootItem
            );
        }

        let api = {
            get element() {
                return element;
            },

            navigate: navigate,
            replace: replace,
        };

        if (
            CBUINavigationView.context !== undefined
        ) {
            throw new Error(
                "There is only one CBUINavigationView allowed per page."
            );
        } else {
            CBUINavigationView.context = api;
        }



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
        function navigate(
            item
        ) {
            if (
                typeof item !== "object"
            ) {
                throw TypeError(
                    `The "item" parameter must be an object`
                );
            }

            if (
                !(item.element instanceof HTMLElement)
            ) {
                throw TypeError(
                    `The "element" property of the "item" parameter ` +
                    `must be an HTMLElement`
                );
            }

            if (
                level === undefined
            ) {
                level = 0;
            } else {
                level += 1;
            }

            let toItem = {
                element: item.element,
                left: item.left,
                rightElements: item.rightElements,
                title: item.title,
            };

            if (
                level > 0
            ) {
                let fromItem = state.items[level - 1];

                if (toItem.left === undefined) {
                    toItem.left = fromItem.title;
                }

                state.items = state.items.slice(0, level);
            }

            toItem.containerElement = CBUINavigationView.containerFromItem(
                toItem
            );

            state.items.push(
                toItem
            );

            if (level === 0) {
                history.replaceState(
                    {
                        level: level
                    },
                    toItem.title
                );
            } else {
                history.pushState(
                    {
                        level: level
                    },
                    toItem.title
                );
            }

            renderLevel(
                level
            );

            window.scrollTo(
                0,
                0
            );
        }
        /* navigate() */



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
        function replace(
            item
        ) {
            if (
                level === undefined
            ) {
                navigate(
                    item
                );

                return;
            }

            let toItem = {
                element: item.element,
                left: item.left,
                rightElements: item.rightElements,
                title: item.title,
            };

            if (level > 0) {
                let fromItem = state.items[level - 1];

                if (toItem.left === undefined) {
                    toItem.left = fromItem.title;
                }
            }

            toItem.containerElement = CBUINavigationView.containerFromItem(
                toItem
            );

            state.items[level] = toItem;

            history.replaceState(
                {
                    level: level
                },
                toItem.title
            );

            renderLevel(
                level
            );

            window.scrollTo(
                0,
                0
            );
        }
        /* replace() */



        /**
         * @NOTE 2021_03_31
         *
         * When CBUINavigationView.navigate() is called, an item is added to the
         * state.items array and history.pushState() is called with an object
         * that has a level property that refers to the index of the state.items
         * array that that current virtual "page" refers to. We use this
         * strategy to be able to use that back and forward arrows in the
         * browser. However, if the user presses the back arrow so many times
         * that they go back to the previous actual page and then press the
         * forward arrow to come back to the current actual page the state
         * variable is reset.
         *
         * If they press the forward arrow again we will no longer have the
         * state necessary to render the UI that was once rendered for that
         * virtual page. When that happens we replace the state for that virtual
         * page to a state with the level 0 instead of crashing and the state
         * will start rebuilding itself if they navigate further on the current
         * actual page again.
         *
         * @param object state
         *
         * @return undefined
         */
        function
        handlePopState(
            popStateEvent
        ) {
            let stateEventLevel = CBModel.valueAsInt(
                popStateEvent.state,
                "level"
            );

            let stateItems = CBModel.valueToArray(
                state,
                "items"
            );

            if (stateItems.length < 1) {
                throw new Error("TODO");
            }

            if (
                stateEventLevel === undefined ||
                stateEventLevel < 0
            ) {
                level = 0;
            } else if (
                stateEventLevel >= stateItems.length
            ) {
                level = stateItems.length - 1;

                history.replaceState(
                    {
                        level,
                    },
                    `has been reset to level ${level}`
                );
            } else {
                level = stateEventLevel;
            }

            renderLevel(
                level
            );
        }
        /* handlePopState() */



        /**
         * @param int level
         *
         * @return undefined
         */
        function
        renderLevel(
            level
        ) {
            let containerElement = state.items[level].containerElement;

            element.textContent = null;

            element.appendChild(
                containerElement
            );
        }
        /* renderLevel() */



        return api;
    },
    /* create() */



    /**
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
    navigate(
        item
    ) {
        if (
            CBUINavigationView.context === undefined
        ) {
            throw new Error(
                "No CBUINavigationView has been created"
            );
        }

        CBUINavigationView.context.navigate(
            item
        );
    },
    /* navigate() */



    /**
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
    replace(
        item
    ) {
        if (
            CBUINavigationView.context === undefined
        ) {
            throw new Error(
                "No CBUINavigationView has been created"
            );
        }

        CBUINavigationView.context.replace(
            item
        );
    },
    /* replace() */

};
