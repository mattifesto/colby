/* global
    CBJavaScript,
*/

(function ()
{
    "use strict";



    CBJavaScript.afterDOMContentLoaded(
        function (
        ) // -> undefined
        {
            let rootElements =
            Array.from(
                document.getElementsByClassName(
                    "CB_View_SVGBarChart1_root_element"
                )
            );



            rootElements.forEach(
                function (
                    rootElement
                ) // -> undefined
                {
                    let currentValueElement =
                    rootElement.getElementsByClassName(
                        "CB_View_SVGBarChart1_currentValue_element"
                    )[0];

                    let barElements =
                    Array.from(
                        rootElement.getElementsByClassName(
                            "CB_View_SVGBarChart1_transparentFullBar_element"
                        )
                    );



                    barElements.forEach(
                        function (
                            barElement
                        ) // -> undefined
                        {
                            barElement.addEventListener(
                                "mouseover",
                                function (
                                ) // -> undefined
                                {
                                    currentValueElement.textContent =
                                    barElement.dataset.value;
                                }
                            );
                        }
                    );
                    // barElements.forEach()

                }
            );
            // rootElements.forEach()

        }
    );
    // CBJavaScript.afterDOMContentLoaded()

}
)();
