/* global
    CB_UI,
    CBDevelopersUserGroup,
    CBJavaScript,
*/

(function ()
{
    "use strict";



    const shared_graphHeightAsPixels =
    100;

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
            let values =
            JSON.parse(
                rootElement.dataset.values
            );

            CB_View_SVGBarChart1_renderSVG2Element(
                svg2Element,
                values
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
     * @param [int] arrayOfBarHeightsArgument
     *
     * @return undefined
     */
    function
    CB_View_SVGBarChart1_renderSVG2Element(
        svg2ElementArgument,
        arrayOfValuesArgument
    ) // -> undefined
    {
        let minimumValue =
        Math.min(...arrayOfValuesArgument);

        let maximumValue =
        Math.max(...arrayOfValuesArgument);

        let minimumBarHeight =
        minimumValue <= 0 ?
        0 :
        (
            shared_graphHeightAsPixels *
            0.2
        );

        let maximumBarHeight =
        shared_graphHeightAsPixels;

        let barHeightRange =
        maximumBarHeight -
        minimumBarHeight;

        for(
            let barIndex = 0;
            barIndex < 28;
            barIndex += 1
        ) {
            let currentValue =
            arrayOfValuesArgument[
                barIndex
            ];

            let currentUnitValue =
            (
                currentValue -
                minimumValue
            ) /
            (
                maximumValue -
                minimumValue
            );

            let barHeight =
            (
                currentUnitValue *
                barHeightRange
            ) +
            minimumBarHeight;

            CB_View_SVGBarChart1_renderBar(
                svg2ElementArgument,
                "CB_View_SVGBarChart1_barBackground_element",
                barIndex,
                maximumBarHeight
            );

            CB_View_SVGBarChart1_renderBar(
                svg2ElementArgument,
                "CB_View_SVGBarChart1_color_gray",
                barIndex,
                barHeight
            );
        }
    }
    // CB_View_SVGBarChart1_renderSVG2Element()



    /**
     * @param Element svg2ElementArgument
     */
    function
    CB_View_SVGBarChart1_renderBar(
        svg2ElementArgument,
        classNameArgument,
        barIndexArgument,
        barHeightArgument,
    ) // -> undefined
    {
        let x =
        (
            10 *
            barIndexArgument
        ) +
        1;

        let width =
        8;

        let y =
        shared_graphHeightAsPixels -
        barHeightArgument;

        let height =
        barHeightArgument;

        let rectElement =
        document.createElementNS(
            svgNamespace,
            "rect"
        );

        rectElement.setAttribute(
            "x",
            `${x}`
        );

        rectElement.setAttribute(
            "y",
            `${y}`
        );

        rectElement.setAttribute(
            "width",
            `${width}`
        );

        rectElement.setAttribute(
            "height",
            `${height}`
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
