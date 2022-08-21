/* global
    CBException,
    CBModel,
*/


(function ()
{
    "use strict";

    let CB_Link =
    {
        getText:
        CB_Link_getText,

        setText:
        CB_Link_setText,

        getURL:
        CB_Link_getURL,

        setURL:
        CB_Link_setURL,
    };

    window.CB_Link =
    CB_Link;



    /**
     * @param object linkModel
     *
     * @return string
     */
    function
    CB_Link_getText(
        linkModel
    ) // -> string
    {
        let linkText =
        CBModel.valueToString(
            linkModel,
            'CB_Link_text_property'
        );

        linkText =
        linkText.trim();

        return linkText;
    }
    // CB_Link_getText()



    /**
     * @param object linkModel
     * @param string newText
     *
     * @return undefined
     */
    function
    CB_Link_setText(
        linkModel,
        newText
    ) // -> undefined
    {
        if (
            typeof newText !==
            "string"
        ) {
            let error =
            new TypeError(
                "The text property value must be a string."
            );

            throw CBException.withError(
                error,
                "",
                "758e39078086e2ea554a15d7616e1415e6b1363a"
            );
        }

        linkModel.CB_Link_text_property =
        newText;
    }
    // CB_Link_setText()



    /**
     * @param object linkModel
     *
     * @return string
     */
    function
    CB_Link_getURL(
        linkModel
    ) // -> string
    {
        let linkURL =
        CBModel.valueToString(
            linkModel,
            'CB_Link_url_property'
        );

        linkURL =
        linkURL.trim();

        return linkURL;
    }
    // CB_Link_getURL()



    /**
     * @param object linkModel
     * @param string newURL
     *
     * @return undefined
     */
    function
    CB_Link_setURL(
        linkModel,
        newURL
    ) // -> undefined
    {
        if (
            typeof newURL !==
            "string"
        ) {
            let error =
            new TypeError(
                "The URL property value must be a string."
            );

            throw CBException.withError(
                error,
                "",
                "ac97c6a9a1f08a1c139ac1d8edbb3e4406e11b38"
            );
        }

        linkModel.CB_Link_url_property =
        newURL;
    }
    // CB_Link_setURL()

}
)();
