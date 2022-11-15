/* global
    CB_UI,
    CBDevelopersUserGroup,
    CBJavaScript,
*/

(function ()
{
    "use strict";



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

}
)();
