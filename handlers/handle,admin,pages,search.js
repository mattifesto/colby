"use strict";

var CBSearchForPagesAdmin = {

    /**
     * @return {Element}
     */
    createElement : function() {
        var element = document.createElement("div");
        element.className = "CBSearchForPagesAdmin";
        var form = document.createElement("form");
        form.action = "";
        var input = document.createElement("input");
        input.autocapitalize = "none";
        input.placeholder = "enter search text";
        input.type = "search";
        var submit = document.createElement("input");
        submit.type = "submit";
        submit.value = "Search";
        var results = document.createElement("div");
        results.className = "results";

        form.appendChild(input);
        form.appendChild(submit);
        element.appendChild(form);
        element.appendChild(results);

        form.addEventListener("submit", CBSearchForPagesAdmin.fetchPages.bind(undefined, {
            inputElement : input,
            resultsElement : results
        }));

        return element;
    },

    /**
     * @param {Element} inputElement
     * @param {Element} resultsElement
     *
     * @return undefined
     */
    fetchPages : function(args, event) {
        event.preventDefault();

        var formData = new FormData();
        formData.append("query-text", args.inputElement.value);

        var xhr = new XMLHttpRequest();
        xhr.onload = CBSearchForPagesAdmin.fetchPagesDidLoad.bind(undefined, {
            resultsElement : args.resultsElement,
            xhr : xhr
        });
        xhr.onerror = CBSearchForPagesAdmin.fetchPagesDidError.bind(undefined, {
            xhr : xhr
        });
        xhr.open("POST", "/api/?class=CBViewPage&function=fetchSearchResults");
        xhr.send(formData);
    },

    /**
     * @return undefined
     */
    fetchPagesDidError : function (args) {
        alert('An error occurred when trying to fetch pages.');
    },

    /**
     * @param {Element} resultsElement
     * @param {XMLHttpRequest} xhr
     *
     * @return undefined
     */
    fetchPagesDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var pages = response.pages;
            var list = CBPageList.createElement({
                pages : pages
            });

            args.resultsElement.textContent = null;
            args.resultsElement.appendChild(list);
        } else {
            Colby.displayResponse(response);
        }
    }
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBSearchForPagesAdmin.createElement());
});
