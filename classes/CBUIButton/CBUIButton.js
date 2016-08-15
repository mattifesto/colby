"use strict"; /* jshint strict: global */

/**
 * This object has the purpose of standardizing button functionality while
 * imposing as little dogma as possible. These comments contain valuable
 * information about how buttons can be used.
 *
 * A button:
 *
 *      - Executes a callback, if one is provided, when clicked.
 *
 *      - Never allows its content to be selected.
 *
 *      - Supports an undefined callback for cases where a button is created
 *        to match the appearance of other nearby buttons but does not
 *        explicitly react to a button click. For instance if the content is an
 *        anchor element.
 *
 * This class leaves out a lot of functionality for simplicity and flexibility.
 *
 *      - The functionality of disabling a button is left to the implementor.
 *        This would be implemented by the callback being aware of the disabled
 *        state of the button and reacting appropriately.
 *
 *        The reason for this is that disable functionality is not uniform.
 *        Sometimes a disabled button will still want to respond to a click
 *        with a message or some other action. In this way, the button is not
 *        technically disabled but is just selecting a different path.
 *
 *      - Changing the callback is not supported because it can be implemented
 *        by the callback itself. Often this functionality is also highly
 *        customized to the specific implementation.
 */
var CBUIButton = { /* exported CBUIButton */

    /**
     * Creates an empty element with a class of "CBUIButton". To complete the
     * button the following actions should be taken:
     *
     *      - Add child elements or at the very least textContent to the
     *        element.
     *
     *      - Use `element.classList.add` to add custom CSS classes to the
     *        element for styling. Do not add additional styles to the
     *        "CBUIButton" class.
     *
     *      - Alternatively add elements with custom CSS classes and style those
     *        if that works better.
     *
     *      - Make any other changes to the element and its children that are
     *        required for your use case.
     *
     * Note: Using `element.classList.add` and `element.classList.remove` is the
     * recommended way to add and remove classes from the returned element.
     *
     * @param object? args
     * @param function? args.callback
     *
     * @return { Element element }
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUIButton";

        if (args && args.callback) {
            element.addEventListener("click", args.callback.bind());
        }

        return {
            element : element,
        };
    },
};
