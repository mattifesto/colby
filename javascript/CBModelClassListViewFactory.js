"use strict";

var CBModelClassListViewFactory = {

    /**
     * @param {function} args.navigate
     *
     * @return {Element}
     */
    createElement : function(args) {
        var element = document.createElement("div");
        element.className = "CBModelClassListView";
        var container = document.createElement("div");
        container.className = "container";

        CBClassMenuItems.forEach(function(item) {
            var itemElement = document.createElement("div");
            itemElement.className = "CBModelClassListItem";
            itemElement.textContent = item.title;

            itemElement.addEventListener("click", CBModelClassListViewFactory.navigateToModelListView.bind(undefined, {
                listClassName : item.itemClassName,
                navigate : args.navigate,
                listTitle : item.title,
            }));

            container.appendChild(itemElement);
        });

        element.appendChild(container);

        return element;
    },

    /**
     * @param {string} state.classNameForModels
     * @param {function} args.fetchInfoForModelsDidLoadCallback
     *
     * @return undefined
     */
    fetchInfoForModels : function(state, args) {
        var formData = new FormData();
        formData.append("className", state.classNameForModels);
        formData.append("pageNumber", 1);

        var xhr = new XMLHttpRequest();
        xhr.onload = CBModelClassListViewFactory.fetchInfoForModelsDidLoad.bind(undefined, {
            fetchInfoForModelsDidLoadCallback : args.fetchInfoForModelsDidLoadCallback,
            xhr : xhr,
        });
        xhr.onerror = CBModelClassListViewFactory.fetchInfoForModelsDidNotLoad.bind(undefined, {
            classNameForModels : state.classNameForModels,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBAdminPageForModels&function=fetchInfoForModels");
        xhr.send(formData);
    },

    /**
     * @param {function} args.fetchInfoForModelsDidLoadCallback
     * @param {XMLHttpRequest} args.xhr
     *
     * @return undefined
     */
    fetchInfoForModelsDidLoad : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.fetchInfoForModelsDidLoadCallback(response.infoForModels);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param {string} args.classNameForModels
     * @param {XMLHttpRequest} args.xhr
     *
     * @return undefined
     */
    fetchInfoForModelsDidNotLoad : function(args) {
        Colby.alert("The model list for models with the class name \"" +
            args.classNameForModels +
            "\" failed to load.");
    },

    /**
     * @param {function} args.navigate
     * @param {string} args.listClassName
     * @param {string} args.listTitle
     *
     * @return undefined
     */
    navigateToModelListView : function(args) {
        var modelListViewElement = CBModelListViewFactory.createElement({
            fetchInfoForModelsCallback : CBModelClassListViewFactory.fetchInfoForModels.bind(undefined, {
                classNameForModels : args.listClassName,
            }),
        });

        args.navigate({
            element : modelListViewElement,
            title : args.listTitle,
        });
    },
};
