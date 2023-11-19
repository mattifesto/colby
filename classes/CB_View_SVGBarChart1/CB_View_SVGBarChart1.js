/* global
    CBJavaScript,
    Chart,
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
     * @param Element rootElement
     *
     * @return undefined
     */
    function
    CB_View_SVGBarChart1_initializeRootElement(
        rootElement
    ) // -> undefined
    {
        let values =
        JSON.parse(
            rootElement.dataset.values
        );

        let titles =
        JSON.parse(
            rootElement.dataset.titles
        );

        let chartjsContainerElement =
        rootElement.getElementsByClassName(
            "CB_View_SVGBarChart1_chartjs_container_element"
        ).item(0);

        if (chartjsContainerElement !== null)
        {
            new Chart(
                chartjsContainerElement,
                {
                    type: 'line',
                    data: {
                        labels: titles,
                        datasets: [
                            {
                                label: 'subscribers',
                                data: values,
                                borderWidth: 1
                            }
                        ]
                    }
                }
            );
        }
    }
    // CB_View_SVGBarChart1_initializeRootElement()

}
)();
