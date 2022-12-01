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
        let sectionElement =
        document.createElement(
            "div"
        );

        sectionElement.className =
        "CBUISection";

        let controller =
        {
            appendItem:
            CBUISection_appendItem,

            get element()
            {
                return sectionElement;
            },
        };

        /**
         * @param object sectionItem
         *
         * @return undefined
         */
        function
        CBUISection_appendItem(
            sectionItem
        ) // -> undefined
        {
            sectionElement.append(
                sectionItem.element
            );
        }

        return controller;
    }
    // CBUISection_create()

}
)();
