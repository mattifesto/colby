"use strict";

var CBNavigationViewFactory = {

    /**
     * @param [{Object}] items
     * @param {string} title
     *
     * @return {Element}
     */
    createView : function(args) {
        var element = document.createElement("div");
        element.className = "CBNavigationView";
        var nav = document.createElement("nav");
        var back = document.createElement("div");
        back.className = "back";
        var title = document.createElement("div");
        title.className = "title";
        var content = document.createElement("div");
        var stack = [];

        var navigate = CBNavigationViewFactory.navigate.bind(undefined, {
            backElement : back,
            contentElement : content,
            stack : stack,
            titleElement : title,
        });

        nav.appendChild(back);
        nav.appendChild(title);
        element.appendChild(nav);
        element.appendChild(content);

        back.addEventListener("click", CBNavigationViewFactory.navigateBack.bind(undefined, {
            backElement : back,
            contentElement : content,
            stack : stack,
            titleElement : title,
        }));

        return {
            element : element,
            navigate : navigate,
        };
    },

    /**
     * @param {Element} state.backElement
     * @param {Element} state.contentElement
     * @param {array} state.stack
     * @param {Element} state.titleElement
     *
     * @param {Element} args.element
     * @param {string} args.title
     *
     * @return undefined
     */
    navigate : function(state, args) {
        if (state.contentElement.firstChild) {
            state.stack.push({
                element : state.contentElement.firstChild,
                title : state.titleElement.textContent,
            });
        }

        state.backElement.textContent = (state.titleElement.textContent.length) > 0 ? "< " + state.titleElement.textContent : '';
        state.contentElement.textContent = null;
        state.contentElement.appendChild(args.element);
        state.titleElement.textContent = args.title;
    },

    navigateBack : function(state) {
        if (state.stack.length > 0) {
            var item = state.stack.pop();

            state.contentElement.textContent = null;
            state.contentElement.appendChild(item.element);
            state.titleElement.textContent = item.title;

            if (state.stack.length > 0) {
                var index = state.stack.length - 1;
                state.backElement.textContent = state.stack[index].title;
            } else {
                state.backElement.textContent = "";
            }
        }
    },
}
