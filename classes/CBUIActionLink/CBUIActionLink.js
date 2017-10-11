"use strict";
/* jshint strict: global */

var CBUIActionLink = {

    /**
     * @param object args
     *
     *      {
     *          callback: function
     *          labelText: string
     *      }
     *
     * @return object
     *
     *      {
     *          disableCallback: function
     *          element: Element
     *          enableCallback: function
     *          updateCallbackCallback: function
     *          updateLabelTextCallback: function
     *      }
     */
    create: function (args) {
        var element = document.createElement("div");
        element.className = "CBUIActionLink";
        element.textContent = args.labelText || "";
        var state = { callback : args.callback };

        element.addEventListener("click", CBUIActionLink.handleLinkWasClicked.bind(undefined, {
            state : state,
        }));

        var enableCallback = CBUIActionLink.enable.bind(undefined, {
            element : element,
            state : state,
        });

        var disableCallback = CBUIActionLink.disable.bind(undefined, {
            element : element,
            state : state,
        });

        var updateLabelTextCallback = CBUIActionLink.updateLabelText.bind(undefined, {
            element : element,
        });

        var updateCallbackCallback = CBUIActionLink.updateCallback.bind(undefined, {
            state : state,
        });

        enableCallback();

        return {
            disableCallback : disableCallback,
            element : element,
            enableCallback : enableCallback,
            updateCallbackCallback : updateCallbackCallback,
            updateLabelTextCallback : updateLabelTextCallback,
        };
    },

    /**
     * @param Element args.element
     * @param object args.state
     *
     * @return undefined
     */
    disable : function (args) {
        args.state.disabled = true;
        args.element.classList.add("disabled");
    },

    /**
     * @param Element args.element
     * @param object args.state
     *
     * @return undefined
     */
    enable : function (args) {
        args.state.disabled = undefined;
        args.element.classList.remove("disabled");
    },

    /**
     * @param object args.state
     *
     * @return undefined
     */
    handleLinkWasClicked : function (args) {
        if (args.state.disabled !== true && args.state.callback !== undefined) {
            args.state.callback.call();
        }
    },

    /**
     * @param Element args.element
     * @param string labelText
     *
     * @return undefined
     */
    updateLabelText : function (args, labelText) {
        args.element.textContent = labelText;
    },

    /**
     * @param object args.state
     * @param function callback
     *
     * @return undefined
     */
    updateCallback : function(args, callback) {
        args.state.callback = callback;
    },
};
