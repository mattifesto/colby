/* global
    CBConvert
*/


(function ()
{
    "use strict";



    let CBUISpecClipboard =
    {
        /**
         * @return [object]
         */
        get specs()
        {
            return CBUISpecClipboard_getSpecs();
        },

        /**
         * @param [object] value
         */
        set specs(
            specsArgument
        ) {
            CBUISpecClipboard_setSpecs(
                specsArgument
            );
        },
    };

    window.CBUISpecClipboard =
    CBUISpecClipboard;



    /**
     * @return [object]
     */
    function
    CBUISpecClipboard_getSpecs(
    ) // -> [object]
    {
        let clipboardAsJSON =
        localStorage.getItem(
            "CBUISpecClipboard"
        );

        if (
            clipboardAsJSON === null
        ) {
            return [];
        }

        let specs =
        JSON.parse(
            clipboardAsJSON
        );

        return specs;
    }
    // CBUISpecClipboard_getSpecs()



    /**
     * @param [object] arrayOfSpecsArgument
     *
     * @return undefined
     */
    function
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

        let arrayOfSpecsAsJSON =
        JSON.stringify(
            arrayOfSpecs
        );

        localStorage.setItem(
            "CBUISpecClipboard",
            arrayOfSpecsAsJSON
        );
    }
    // CBUISpecClipboard_setSpecs()

}
)();
