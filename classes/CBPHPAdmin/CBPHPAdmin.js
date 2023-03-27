/* global
    CBJavaScript,
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,

    CBPHPAdmin_values,
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

        CBPHPAdmin_values.forEach(
            function (
                value
            ) // -> undefined
            {
                let sectionItem =
                CBUISectionItem4.create();

                let stringsPart =
                CBUIStringsPart.create();

                stringsPart.string1 =
                value.CBPHPAdmin_values_name_property;

                stringsPart.string2 =
                value.CBPHPAdmin_values_value_property ||
                Colby.nonBreakingSpace;

                stringsPart.element.classList.add(
                    "CBUIStringsPart_leftandright"
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
