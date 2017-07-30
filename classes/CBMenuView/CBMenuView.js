"use strict"; /* jshint strict: global */

var CBMenuView = {

    instances: [],
    viewportWidth: undefined,

    /**
     * @return undefined
     */
    handleResize: function () {
        if ((CBMenuView.viewportWidth > 736) !== (window.innerWidth > 736)) {
            CBMenuView.instances.forEach(function (element) {
                element.classList.remove("open");
            });
        }

        CBMenuView.viewportWidth = window.innerWidth;
    },

    /**
     * @return undefined
     */
    initialize: function () {
        var button, element;
        var viewElements = document.getElementsByClassName("CBMenuView");

        for (var i = 0; i < viewElements.length; i++) {
            element = viewElements[i];

            if (!element.classList.contains("CBMenuView_initialized")) {
                if (element.classList.contains("CBMenuView_submenu1")) {
                    button = element.getElementsByClassName("center")[0];
                } else {
                    button = element.getElementsByClassName("left")[0];
                }

                button.addEventListener("click", CBMenuView.toggle.bind(undefined, element));

                element.classList.add("CBMenuView_initialized");

                CBMenuView.instances.push(element);
            }
        }

        window.addEventListener("resize", CBMenuView.handleResize);

        CBMenuView.viewportWidth = window.innerWidth;
    },

    /**
     * @param Element element
     *
     *      A CBMenuView element.
     *
     * @return undefined
     */
    toggle: function (element) {
        element.classList.toggle("open");
    },
};

document.addEventListener("DOMContentLoaded", CBMenuView.initialize);
