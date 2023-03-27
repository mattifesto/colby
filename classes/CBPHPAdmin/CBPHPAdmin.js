/* global
    CBJavaScript,
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,

    CBPHPAdmin_iniValues,
*/

(function ()
{
    "use strict";

    CBJavaScript.afterDOMContentLoaded(
        function ()
        {
            CBPHPAdmin_init();
        }
    );


    // -- private functions



    /**
     * @return undefined
     */
    function
    CBPHPAdmin_init(
    ) // -> undefined
    {
        let mainElement =
        document.getElementsByTagName(
            "main"
        )[0];

        let sectionElement =
        CBUI.createSection();

        mainElement.appendChild(
            CBUI.createHalfSpace()
        );

        Object.keys(
            CBPHPAdmin_iniValues
        ).forEach(
            function (key)
            {
                let sectionItem =
                CBUISectionItem4.create();

                let stringsPart =
                CBUIStringsPart.create();

                stringsPart.string1 =
                key;

                stringsPart.string2 =
                CBPHPAdmin_iniValues[key] ||
                Colby.nonBreakingSpace;

                stringsPart.element.classList.add(
                    "keyvalue"
                );

                stringsPart.element.classList.add(
                    "selectable"
                );

                sectionItem.appendPart(
                    stringsPart
                );

                sectionElement.appendChild(
                    sectionItem.element
                );
            }
        );

        mainElement.appendChild(
            sectionElement
        );

        mainElement.appendChild(
            CBUI.createHalfSpace()
        );
    }
    // CBPHPAdmin_init()

}
)() ;
