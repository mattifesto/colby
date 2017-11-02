"use strict";
/* jshint strict: global */
/* exported CBUIActionLink */

var CBUIActionLink = {

    /**
     * @param object args
     *
     *      {
     *          callback: function
     *          labelText: string (deprecated use textContent)
     *          textContent: string
     *      }
     *
     * @return object
     *
     *      {
     *          callback: function (getter, setter)
     *          disable: function
     *          disableCallback: function (deprecated use disable)
     *          element: Element
     *          enable: function
     *          enableCallback: function (deprecated use enable)
     *          textContent: string (getter, setter)
     *          updateCallbackCallback: function (deprecated use callback property)
     *          updateLabelTextCallback: function (deprecated use textContent property)
     *      }
     */
    create: function (args) {
        var callback = args.callback;
        var isDisabled = false;
        var element = document.createElement("div");
        element.className = "CBUIActionLink";
        element.textContent = args.textContent || args.labelText || "";

        element.addEventListener("click", function() {
            if (!isDisabled && (typeof callback === "function")) {
                callback.call();
            }
        });

        enable();

        return {
            get callback() {
                return callback;
            },
            set callback(value) {
                callback = value;
            },
            disable: disable,
            disableCallback: disable, /* deprecated use disable */
            element: element,
            enable: enable,
            enableCallback: enable, /* deprecated use enable */
            get textContent() {
                return element.testContent;
            },
            set textContent(value) {
                element.textContent = value;
            },
            updateCallbackCallback: updateCallback, /* deprecated use callback */
            updateLabelTextCallback: updateLabel /* deprecated use textContent */,
        };

        /* closure */
        function disable() {
            isDisabled = true;
            element.classList.add("disabled");
        }

        /* closure */
        function enable() {
            isDisabled = false;
            element.classList.remove("disabled");
        }

        /* closure deprecated */
        function updateCallback(value) {
            callback = value;
        }

        /* closure deprecated */
        function updateLabel(value) {
            element.textContent = value;
        }
    },
};
