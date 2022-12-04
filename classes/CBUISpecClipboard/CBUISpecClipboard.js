/* global
    CBConvert
*/


(function ()
{
    "use strict";



    let CBUISpecClipboard =
    {
        getSpecs :
        CBUISpecClipboard_getSpecs,

        setSpecs :
        CBUISpecClipboard_setSpecs,
    };

    window.CBUISpecClipboard =
    CBUISpecClipboard;



    /**
     * @return [object]
     */
    async function
    CBUISpecClipboard_getSpecs(
    ) // -> [object]
    {
        let clipboardText =
        await navigator.clipboard.readText();

        let specs;

        try
        {
            let potentialClipboardModel =
            JSON.parse(
                clipboardText
            );

            let clipboardModel =
            CBConvert.valueAsModel(
                potentialClipboardModel,
                "CBUISpecClipboard"
            );

            if (
                clipboardModel !== undefined
            ) {
                specs =
                CBConvert.valueToArray(
                    clipboardModel.CBUISpecClipboard_arrayOfSpecs
                );
            }
        }

        catch (
            error
        ) {
            specs =
            [];
        }

        return specs;
    }
    // CBUISpecClipboard_getSpecs()



    /**
     * @param [object] arrayOfSpecsArgument
     *
     * @return undefined
     */
    async function
    CBUISpecClipboard_setSpecs(
        arrayOfSpecsArgument
    ) // -> undefined
    {
        let arrayOfSpecs =
        [];

        let arrayOfSpecsArgumentIsAnArray =
        Array.isArray(
            arrayOfSpecsArgument
        );

        if (
            arrayOfSpecsArgumentIsAnArray
        ) {
            arrayOfSpecs =
            arrayOfSpecsArgument;
        }

        for (
            let specIndex = 0;
            specIndex < arrayOfSpecs.length;
            specIndex++
        ) {
            let currentSpec =
            arrayOfSpecs[
                specIndex
            ];

            let currentSpecAsModel =
            CBConvert.valueAsModel(
                currentSpec
            );

            if (
                currentSpecAsModel === undefined
            ) {
                arrayOfSpecs =
                [];

                break;
            }
        }

        let clipboardModel =
        {
            "className" :
            "CBUISpecClipboard",

            "CBUISpecClipboard_arrayOfSpecs" :
            arrayOfSpecs,
        };

        let clipboardModelAsJSON =
        JSON.stringify(
            clipboardModel
        );

        await navigator.clipboard.writeText(
            clipboardModelAsJSON
        );
    }
    // CBUISpecClipboard_setSpecs()

}
)();
