/* global
    CB_UI,
    CBDevelopersUserGroup,
    CBJavaScript,
*/

(function ()
{
    "use strict";



    const svgNamespace =
    "http://www.w3.org/2000/svg";



    CBJavaScript.afterDOMContentLoaded(
        function ()
        {
            let rootElements =
            Array.from(
                document.getElementsByClassName(
                    "CB_View_SVGBarChart1_root_element"
                )
            );

            rootElements.forEach(
                function (rootElement)
                {
                    CB_View_SVGBarChart1_initializeRootElement(
                        rootElement
                    );
                }
            );
        }
    );
    // CBJavaScript.afterDOMContentLoaded()



    /**
     * @param Element parentElement
     *
     * @return undefined
     */
    function
    CB_View_SVGBarChart1_createDeveloperElement(
        parentElement
    ) // -> undefined
    {
        let developerElement =
        document.createElement(
            "div"
        );

        parentElement.append(
            developerElement
        );

        developerElement.textContent =
        "(developer)";
    }
    // CB_View_SVGBarChart1_createDeveloperElement()



    /**
     * @param Element rootElement
     *
     * @return undefined
     */
    function
    CB_View_SVGBarChart1_initializeRootElement(
        rootElement
    ) // -> undefined
    {
        let svg2Element =
        rootElement.getElementsByClassName(
            "CB_View_SVGBarChart1_svg2_element"
        ).item(0);

        if (
            svg2Element !==
            null
        ) {
            CB_View_SVGBarChart1_renderSVG2Element(
                svg2Element
            );
        }

        let contentElement =
        rootElement.getElementsByClassName(
            "CB_View_SVGBarChart1_content_element"
        )[0];

        let currentValueElement =
        document.createElement(
            "div"
        );

        contentElement.append(
            currentValueElement
        );

        currentValueElement.className =
        "CB_View_SVGBarChart1_currentValue_element";

        currentValueElement.textContent =
        CB_UI.getNonBreakingSpaceCharacter();

        if (
            CBDevelopersUserGroup.currentUserIsMember()
        ) {
            CB_View_SVGBarChart1_createDeveloperElement(
                contentElement
            );
        }

        let barElements =
        Array.from(
            rootElement.getElementsByClassName(
                "CB_View_SVGBarChart1_transparentFullBar_element"
            )
        );

        barElements.forEach(
            function (barElement)
            {
                barElement.addEventListener(
                    "mouseover",
                    function ()
                    {
                        currentValueElement.textContent =
                        barElement.dataset.value;
                    }
                );
            }
        );
    }
    // CB_View_SVGBarChart1_initializeRootElement()



    /**
     * @param Element svg2ElementArgument
     */
    function
    CB_View_SVGBarChart1_renderSVG2Element(
        svg2ElementArgument
    ) // -> undefined
    {
        let rectElement =
        document.createElementNS(
            svgNamespace,
            "rect"
        );

        rectElement.setAttribute(
            "x",
            "0"
        );

        rectElement.setAttribute(
            "y",
            "0"
        );

        rectElement.setAttribute(
            "width",
            "7"
        );

        rectElement.setAttribute(
            "height",
            "100"
        );

        rectElement.setAttribute(
            "class",
            "CB_View_SVGBarChart1_barBackground_element"
        );

        svg2ElementArgument.appendChild(
            rectElement
        );
    }
    // CB_View_SVGBarChart1_renderSVG2Element()

}
)();
