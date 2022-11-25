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
        const graphHeightAsPixels =
        100;

        for(
            let barIndex = 0;
            barIndex < 28;
            barIndex += 1
        ) {
            let x =
            (
                10 *
                barIndex
            ) +
            1;

            let y =
            0;

            let width =
            8;

            let height =
            graphHeightAsPixels;

            CB_View_SVGBarChart1_renderRect(
                svg2ElementArgument,
                "CB_View_SVGBarChart1_barBackground_element",
                x,
                y,
                width,
                height
            );
        }
    }
    // CB_View_SVGBarChart1_renderSVG2Element()



    /**
     * @param Element svg2ElementArgument
     */
    function
    CB_View_SVGBarChart1_renderRect(
        svg2ElementArgument,
        classNameArgument,
        xArgument,
        yArgument,
        widthArgument,
        heightArgument
    ) // -> undefined
    {
        let rectElement =
        document.createElementNS(
            svgNamespace,
            "rect"
        );

        rectElement.setAttribute(
            "x",
            `${xArgument}`
        );

        rectElement.setAttribute(
            "y",
            `${yArgument}`
        );

        rectElement.setAttribute(
            "width",
            `${widthArgument}`
        );

        rectElement.setAttribute(
            "height",
            `${heightArgument}`
        );

        rectElement.setAttribute(
            "class",
            classNameArgument
        );

        svg2ElementArgument.appendChild(
            rectElement
        );
    }
    // CB_View_SVGBarChart1_renderRect()

}
)();
