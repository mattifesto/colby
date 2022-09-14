(function ()
{
    "use strict";

    window.CBJavaScript =
    {
        afterDOMContentLoaded:
        CBJavaScript_afterDOMContentLoaded,
    };



    /**
     * @param function callback
     *
     * @return undefined
     */
    function
    CBJavaScript_afterDOMContentLoaded(
        callback
    ) // -> undefined
    {
        if (
            document.readyState ===
            "loading"
        ) {
            document.addEventListener(
                "DOMContentLoaded",
                callback
            );
        }

        else
        {
            callback();
        }
    }
    /* afterDOMContentLoaded() */

}
)();
