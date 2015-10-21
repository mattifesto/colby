"use strict";

var CBEditableModelClassesViewFactory = {

    /**
     * @param {function} navigation.navigate
     *
     * @return {Element}
     */
    createElement : function(args, navigation) {
        var items = CBClassMenuItems.map(function(classMenuItem) {
            return {
                title : classMenuItem.title,
            };
        });

        var element = document.createElement("div");

        items.forEach(function(item) {
            var itemElement = document.createElement("div");
            itemElement.textContent = item.title;
            itemElement.addEventListener("click", function() {
                var blah = Blah.createElement();

                navigation.navigate({
                    element : blah,
                    title : item.title,
                });
            })
            element.appendChild(itemElement);
        });

        return element;
    },
};

var Blah = {
    createElement : function(args, navigation) {
        var element = document.createElement("div");
        element.style.backgroundColor = "hsl(0, 50%, 80%)";
        element.style.minHeight = "100px";

        return element;
    },
};
