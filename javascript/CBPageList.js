"use strict";

var CBPageList = {

        selectedPageID : undefined,

        /**
         * @param {Array} pages
         *
         * @return {Element}
         */
        createElement : function(args) {
            var element = document.createElement("div");
            element.className = "CBPageListView";
            var list = document.createElement("div");
            list.className = "CBPageList";

            for (var i = 0; i < args.pages.length; i++) {
                list.appendChild(CBPageList.createPageElement({
                    page : args.pages[i]
                }));
            }

            element.appendChild(list);

            return element;
        },

        /**
         * @param {Object} page
         *
         * @return {Element}
         */
        createPageElement : function(args) {
            var element = document.createElement("div");
            element.className = "CBPageListPage";
            var description = document.createElement("div");
            description.className = "description";
            description.textContent = args.page.title;
            var edit = document.createElement("div");
            edit.className = "button edit";
            edit.textContent = "edit";
            var copy = document.createElement("div");
            copy.className = "button copy";
            copy.textContent = "copy";
            var trash = document.createElement("div");
            trash.className = "button trash";
            trash.textContent = "trash";

            element.appendChild(description);
            element.appendChild(edit);
            element.appendChild(copy);
            element.appendChild(trash);

            element.addEventListener("click", CBPageList.handlePageElementWasClicked.bind(undefined, {
                element : element,
                ID : args.page.dataStoreID
            }));

            edit.addEventListener("click", CBPageList.handlePageElementEditWasClicked.bind(undefined, {
                ID : args.page.dataStoreID
            }));

            copy.addEventListener("click", CBPageList.handlePageElementCopyWasClicked.bind(undefined, {
                IDToCopy : args.page.dataStoreID
            }));

            trash.addEventListener("click", CBPageList.handlePageElementTrashWasClicked.bind(undefined, {
                element : element,
                ID : args.page.dataStoreID
            }));

            return element;
        },

        /**
         * @param {hex160} IDToCopy
         *
         * @return undefined
         */
        handlePageElementCopyWasClicked : function(args) {
            location.href = '/admin/pages/edit/?data-store-id=' +
                            Colby.random160() +
                            '&id-to-copy=' +
                            args.IDToCopy;
        },

        /**
         * @param {hex160} ID
         *
         * @return undefined
         */
        handlePageElementEditWasClicked : function(args) {
            location.href = '/admin/pages/edit/?data-store-id=' + args.ID;
        },

        /**
         * @param {Element} element
         * @param {hex160} ID
         *
         * @return undefined
         */
        handlePageElementTrashWasClicked : function(args) {
            var formData = new FormData();
            formData.append("dataStoreID", args.ID);

            var xhr = new XMLHttpRequest();
            xhr.onload = CBPageList.handlePageElementTrashRequestDidLoad.bind(undefined, {
                element : args.element,
                xhr : xhr,
            });

            xhr.open("POST", "/admin/pages/api/move-to-the-trash/", true);
            xhr.send(formData);
        },

        /**
         * @param {Element} args.element
         * @param {XMLHttpRequest} args.xhr
         *
         * @return undefined
         */
        handlePageElementTrashRequestDidLoad : function(args) {
            var response = Colby.responseFromXMLHttpRequest(args.xhr);

            if (response.wasSuccessful) {
                args.element.parentElement.removeChild(args.element);
            } else {
                Colby.displayResponse(response);
            }
        },

        /**
         * @param {Element} element
         * @param {string} ID
         *
         * @return undefined
         */
        handlePageElementWasClicked : function(args) {
            var elements = document.querySelectorAll(".CBPageListPage.selected");

            for (var i = 0; i < elements.length; i++) {
                elements[i].classList.remove("selected");
            }

            args.element.classList.add("selected");
            CBPageList.selectedPageID = args.ID;
        }
};
