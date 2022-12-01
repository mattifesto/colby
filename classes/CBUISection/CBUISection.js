(function ()
{
    "use strict";



    let CBUISection =
    {
        create:
        CBUISection_create,
    };

    window.CBUISection =
    CBUISection;



    /**
     * @return object
     *
     *      {
     *          appendItem(sectionItem)
     *
     *          element: Element (readonly)
     *      }
     */
    function
    CBUISection_create(
    ) // -> object
    {
        var element = document.createElement("div");
        element.className = "CBUISection";

        return {
            appendItem: appendItem,

            get element() {
                return element;
            },
        };

        /**
         * @param object sectionItem
         *
         * @return undefined
         */
        function appendItem(sectionItem) {
            element.appendChild(sectionItem.element);
        }
    }
    // CBUISection_create()

}
)();
