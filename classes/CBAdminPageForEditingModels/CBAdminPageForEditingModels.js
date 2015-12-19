"use strict";

var CBAdminPageForEditingModels = {

    /**
     * @param   {Object} spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element = document.createElement("pre");
        element.textContent = JSON.stringify(args.spec, null, 4);

        return element;
    },

    /**
     * @param {Object} args.spec
     *
     * @return {Element}
     */
    createHeader : function(args) {
        var element = document.createElement("div");
        element.className = "CBUIHeader";
        var left = document.createElement("div");
        left.className = "left";
        var center = document.createElement("div");
        center.className = "center";
        center.textContent = (window.CBModelClassInfo ? CBModelClassInfo.singularTitle : args.spec.className) + " Editor";
        var right = document.createElement("div");
        right.className = "right";

        element.appendChild(left);
        element.appendChild(center);
        element.appendChild(right);

        return element;
    },

    /**
     * @return  undefined
     */
    handleDOMContentLoaded : function() {
        // If the user has been navigating and reloads the page then the model
        // will have been removed from memory. If some sort of state has been
        // pushed it will refer to parts of that non-existent model. We need to
        // reset the editor and calling replaceState will do that.
        history.replaceState(undefined, undefined);

        var formData = new FormData();
        formData.append("className", CBModelClassName);
        formData.append("ID", CBModelID);

        var xhr = new XMLHttpRequest();
        xhr.onload = CBAdminPageForEditingModels.handleModelLoaded.bind(undefined, {
            xhr : xhr
        });
        xhr.onerror = function() {
            alert('An error occured when trying to retreive the data.');
        };

        xhr.open("POST", "/api/?class=CBModels&function=fetchSpec");
        xhr.send(formData);
    },

    /**
     * @param   {XMLHttpRequest} xhr
     *
     * @return  undefined
     */
    handleModelLoaded : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var spec = response.spec || { ID : CBModelID, className : CBModelClassName };
            var navigationState = { stack : [spec] }

            CBAdminPageForEditingModels.renderEditor({
                navigationState : navigationState,
            });

            window.addEventListener("popstate", CBAdminPageForEditingModels.handlePopState.bind(undefined, {
                navigationState : navigationState,
            }));
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param Object args.navigationState
     *
     * @return undefined
     */
    handlePopState : function (args, event) {
        CBAdminPageForEditingModels.renderEditor({
            navigationState : args.navigationState,
        });
    },

    /**
     * @param   {Object}            info
     * @param   {Object}            spec
     * @param   {XMLHttpRequest}    xhr
     *
     * @return  undefined
     */
    handleSaveCompleted : function(args) {
        args.info.saving    = false;
        var response        = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.info.countOfFailures   = undefined;
            args.spec.version           = args.spec.version ? (args.spec.version + 1) : 1;

            /**
             * If the save request took so long that edits have been made
             * and the save timeout has occurred, request another save
             * immediately.
             */

            if (args.info.pending) {
                args.info.pending = undefined;

                CBAdminPageForEditingModels.save({
                    info    : args.info,
                    spec    : args.spec
                });
            }
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param   {Object}            info
     * @param   {Object}            spec
     * @param   {XMLHttpRequest}    xhr
     *
     * @return  undefined
     */
    handleSaveFailed : function(args) {
        args.info.saving            = false;
        args.info.countOfFailures   = args.info.countOfFailures ? (args.info.countOfFailures + 1) : 1;

        if (args.info.countOfFailures < 5) {

            /**
             * Behave is if `info.pending` is set which may be the case.
             */

            args.info.pending = false;

            CBAdminPageForEditingModels.save({
                info    : args.info,
                spec    : args.spec
            });
        } else {
            Colby.setPanelText("After multiple attempts it appers the server is not responding to save requests.");
            Colby.showPanel();
        }
    },

    /**
     * @param   {Object} info
     * @param   {Object} spec
     *
     * @return  undefined
     */
    handleSaveTimeout : function(args) {

        /**
         * The timer is finished so clear the timeoutID.
         */

        args.info.timeoutID = undefined;

        /**
         * If we're waiting for a save request to complete and another timeout
         * has occurred set a flag (`pending`) that means another save request
         * should be sent immediately when current one completes.
         */

        if (args.info.saving) {
            args.info.pending = true;
            return;
        }

        /**
         * Save the spec.
         */

        CBAdminPageForEditingModels.save({
            info    : args.info,
            spec    : args.spec
        });
    },

    /**
     * @param   {Object} info
     * @param   {Object} spec
     *
     * @return  undefined
     */
    handleSpecChanged : function(args) {

        /**
         * If a save is pending we're going to save again as soon as the current
         * save request has completed so there's nothing else to do.
         */

        if (args.info.pending) {
            return;
        }

        /**
         * If there's a current timer running, stop it.
         */

        if (args.info.timeoutID) {
            window.clearTimeout(args.info.timeoutID);
        }

        /**
         * Create a new timer.
         */

        args.info.timeoutID = window.setTimeout(CBAdminPageForEditingModels.handleSaveTimeout.bind(undefined, {
            info            : args.info,
            spec            : args.spec
        }), 2000);
    },

    /**
     * @param Object args.navigationState
     *
     * @return undefined
     */
    navigate : function (args, spec) {
        var index = (history.state) ? history.state.index : 0;

        index++;
        args.navigationState.stack.splice(index, Number.MAX_VALUE, spec);
        history.pushState({ index : index }, undefined);

        CBAdminPageForEditingModels.renderEditor({
            navigationState : args.navigationState,
        });
    },

    /**
     * @param Object args.navigationState
     *
     * @return undefined
     */
    renderEditor : function(args) {
        var index = (history.state) ? history.state.index : 0;
        var spec = args.navigationState.stack[index];
        var editorFactory = window[spec.className + "EditorFactory"] || CBEditorWidgetFactory;
        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;
        var handleSpecChanged = CBAdminPageForEditingModels.handleSpecChanged.bind(undefined, {
            info : {},
            spec : args.navigationState.stack[0],
        });

        main.appendChild(CBAdminPageForEditingModels.createHeader({
            spec : spec
        }));

        main.appendChild(editorFactory.createEditor({
            handleSpecChanged : handleSpecChanged,
            navigateCallback : CBAdminPageForEditingModels.navigate.bind(undefined, { navigationState : args.navigationState, }),
            spec : spec
        }));
    },

    /**
     * @param   {Object} info
     * @param   {Object} spec
     *
     * @return  undefined
     */
    save : function(args) {

        /**
         * A save is about to happen, so set the `saving` property.
         */

        args.info.saving = true;

        /**
         * Send a save request to the server.
         */

        var formData    = new FormData();
        var xhr         = new XMLHttpRequest();
        xhr.onload      = CBAdminPageForEditingModels.handleSaveCompleted.bind(undefined, {
            info        : args.info,
            spec        : args.spec,
            xhr         : xhr
        });
        xhr.onerror     = CBAdminPageForEditingModels.handleSaveFailed.bind(undefined, {
            info        : args.info,
            spec        : args.spec,
            xhr         : xhr
        });

        formData.append("specAsJSON", JSON.stringify(args.spec));
        xhr.open("POST", "/api/?class=CBModels&function=save");
        xhr.send(formData);
    }
};

document.addEventListener("DOMContentLoaded", CBAdminPageForEditingModels.handleDOMContentLoaded);
