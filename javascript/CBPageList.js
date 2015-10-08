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
            element.className = "CBPageList";
            var i;

            for (i = 0; i < args.pages.length; i++) {
                element.appendChild(CBPageList.createPageElement({
                    page : args.pages[i]
                }));
            }

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
            var trash = document.createElement("div");
            trash.className = "button trash";
            trash.textContent = "trash";

            element.appendChild(description);
            element.appendChild(edit);
            element.appendChild(trash);

            element.addEventListener("click", CBPageList.handlePageElementWasClicked.bind(undefined, {
                element : element,
                ID : args.page.dataStoreID
            }));

            edit.addEventListener("click", CBPageList.handlePageElementEditWasClicked.bind(undefined, {
                ID : args.page.dataStoreID
            }));

            trash.addEventListener("click", CBPageList.handlePageElementTrashWasClicked.bind(undefined, {
                element : element,
                ID : args.page.dataStoreID
            }));

            return element;
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
                element : args.element
            });

            xhr.open("POST", "/admin/pages/api/move-to-the-trash/", true);
            xhr.send(formData);
        },

        /**
         * @param {Element} element
         *
         * @return undefined
         */
        handlePageElementTrashRequestDidLoad : function(args) {
            args.element.parentElement.removeChild(args.element);
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
