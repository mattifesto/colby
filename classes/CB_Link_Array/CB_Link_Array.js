/* global
    CBModel,
*/


(function()
{
    "use strict";
    
    window.CB_Link_Array =
    {
        getLinks:
        CB_Link_Array_getLinks,
    };



    /**
     * @param object linkArrayModel
     *
     * @return [<CB_Link model>]
     */
    function
    CB_Link_Array_getLinks(
        linkArrayModel
    ) // -> [<CB_Link model>]
    {
        let links =
        CBModel.valueToArray(
            linkArrayModel,
            "CB_Link_Array_links_property"
        );

        if (
            linkArrayModel.CB_Link_Array_links_property !==
            links
        ) {
            linkArrayModel.CB_Link_Array_links_property =
            links;
        }

        return links;
    }
    // CB_Link_Array_getLinks()

}
)();
