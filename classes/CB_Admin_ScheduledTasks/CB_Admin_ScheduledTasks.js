/* global
    CBAjax,
    CBConvert,
    CBModel,
    CBUIPanel,
    Colby,
*/


(function ()
{
    let CB_Admin_ScheduledTasks_shared_containerElement;
    let CB_Admin_ScheduledTasks_shared_taskClassNameSelectorElement;
    let CB_Admin_ScheduledTasks_shared_sheduledTaskObjects;



    {
        let mainElement =
        document.getElementsByTagName(
            "main"
        )[0];

        mainElement.append(
            CB_Admin_ScheduledTasks_createRootElement()
        );

        CB_Admin_ScheduledTasks_refresh();
    }



    /**
     * @param object scheduledTaskObject
     *
     * @return Element
     */
    function
    CB_Admin_ScheduledTasks_createLine1Element(
        scheduledTaskObject
    ) // -> Element
    {
        let containerElement =
        document.createElement(
            "div"
        );

        let taskClassName =
        CBModel.valueToString(
            scheduledTaskObject,
            "CBTasks2_fetchScheduledTasks_taskClassName"
        );

        let taskPriority =
        CBModel.valueAsInt(
            scheduledTaskObject,
            "CBTasks2_fetchScheduledTasks_taskPriority"
        );

        containerElement.textContent =
        `${taskPriority} ${taskClassName}`;

        return containerElement;
    }
    // CB_Admin_ScheduledTasks_createLine1Element()



    /**
     * @param object scheduledTaskObject
     *
     * @return Element
     */
    function
    CB_Admin_ScheduledTasks_createLine2Element(
        scheduledTaskObject
    ) // -> Element
    {
        let containerElement =
        document.createElement(
            "div"
        );

        let targetModelCBID =
        CBModel.valueAsCBID(
            scheduledTaskObject,
            "CBTasks2_fetchScheduledTasks_targetModelCBID"
        );

        let anchorElement =
        document.createElement(
            "a"
        );

        containerElement.append(
            anchorElement
        );

        anchorElement.href =
        `/admin/?c=CBModelInspector&ID=${targetModelCBID}`;

        anchorElement.style.fontSize =
        "80%";

        anchorElement.textContent =
        targetModelCBID;

        return containerElement;
    }
    // CB_Admin_ScheduledTasks_createLine2Element()



    /**
     * @return Element
     */
    function
    CB_Admin_ScheduledTasks_createRootElement(
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_Admin_ScheduledTasks_element_root";



        /**
         * @NOTE 2022_09_24_1664040448
         *
         *      The select element should eventually be replaced with a Colby
         *      selector.
         */

        let selectorElement =
        document.createElement(
            "select"
        );

        selectorElement.addEventListener(
            "change",
            function (
            ) // -> undefined
            {
                CB_Admin_ScheduledTasks_render();
            }
        );

        rootElement.append(
            selectorElement
        );

        CB_Admin_ScheduledTasks_shared_taskClassNameSelectorElement =
        selectorElement;




        /* container */

        CB_Admin_ScheduledTasks_shared_containerElement =
        document.createElement(
            "div"
        );

        rootElement.append(
            CB_Admin_ScheduledTasks_shared_containerElement
        );



        return rootElement;
    }
    // CB_Admin_ScheduledTasks_createRootElement()



    /**
     * @param object scheduledTaskObject
     *
     * @return Element
     */
    function
    CB_Admin_ScheduledTasks_createTimeElement(
        scheduledTaskObject
    ) // -> Element
    {
        let unixTimestamp =
        CBModel.valueAsInt(
            scheduledTaskObject,
            'CBTasks2_fetchScheduledTasks_timestamp'
        );

        let containerElement =
        document.createElement(
            "div"
        );

        let timeElement =
        Colby.unixTimestampToElement(
            unixTimestamp,
            "Colby_time_element_style_compact"
        );

        containerElement.append(
            timeElement
        );

        timeElement.style.fontSize =
        "80%";

        timeElement.style.color =
        "var(--CBTextColor3)";

        return containerElement;
    }
    // CB_Admin_ScheduledTasks_createTimeElement()



    /**
     * @param [object] arrayOfScheduledTaskObjects
     *
     * @return undefined
     */
    function
    CB_Admin_ScheduledTasks_populateTaskClassNameSelector(
    ) // -> undefined
    {
        let arrayOfTaskClassNames =
        CB_Admin_ScheduledTasks_shared_sheduledTaskObjects.reduce(
            function (
                arrayOfTaskClassNames,
                currentTaskObject
            ) // --> [object]
            {
                let currentTaskClassName =
                CBModel.valueToString(
                    currentTaskObject,
                    "CBTasks2_fetchScheduledTasks_taskClassName"
                );

                let arrayOfTaskClassNamesContainsCurrentTaskClassName =
                arrayOfTaskClassNames.includes(
                    currentTaskClassName
                );


                if (
                    arrayOfTaskClassNamesContainsCurrentTaskClassName !==
                    true
                ) {
                    arrayOfTaskClassNames.push(
                        currentTaskClassName
                    );
                }

                return arrayOfTaskClassNames;
            },
            [] // initial value
        );

        arrayOfTaskClassNames.unshift(
            ""
        );



        let selectorElement =
        CB_Admin_ScheduledTasks_shared_taskClassNameSelectorElement;

        selectorElement.textContent =
        "";



        arrayOfTaskClassNames.forEach(
            function (
                currentTaskClassName
            ) // -> undefined
            {
                let optionElement =
                document.createElement(
                    "option"
                );

                if (
                    currentTaskClassName ===
                    ""
                ) {
                    optionElement.textContent =
                    "All";

                    optionElement.value =
                    "";
                }

                else
                {
                    optionElement.textContent =
                    currentTaskClassName;
                }

                selectorElement.append(
                    optionElement
                );
            }
        );
    }
    // CB_Admin_ScheduledTasks_populateTaskClassNameSelector()



    /**
     * @return undefined
     */
    async function
    CB_Admin_ScheduledTasks_refresh(
    ) // -> undefined
    {
        try
        {
            let result =
            await
            CBAjax.call2(
                "CB_Ajax_FetchScheduledTasks"
            );

            CB_Admin_ScheduledTasks_shared_sheduledTaskObjects =
            CBModel.valueToArray(
                result,
                "CB_Ajax_FetchScheduledTasks_scheduledTasks"
            );

            CB_Admin_ScheduledTasks_populateTaskClassNameSelector();

            CB_Admin_ScheduledTasks_render();
        }
        // try

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );

            throw error;
        }
        // catch

    }
    // CB_Admin_ScheduledTasks_refresh()



    function
    CB_Admin_ScheduledTasks_render(
    ) // --> undefined
    {
        CB_Admin_ScheduledTasks_shared_containerElement.textContent =
        "";

        let scheduledTasksCount =
        CB_Admin_ScheduledTasks_shared_sheduledTaskObjects.length;

        let selectedTaskClassName =
        CBConvert.valueToString(
            CB_Admin_ScheduledTasks_shared_taskClassNameSelectorElement.value
        );

        for (
            let scheduledTaskIndex = 0;
            scheduledTaskIndex < scheduledTasksCount;
            scheduledTaskIndex++
        ) {
            let scheduledTaskObject =
            CB_Admin_ScheduledTasks_shared_sheduledTaskObjects[
                scheduledTaskIndex
            ];

            let taskClassName =
            CBModel.valueToString(
                scheduledTaskObject,
                "CBTasks2_fetchScheduledTasks_taskClassName"
            );

            if (
                selectedTaskClassName !==
                "" &&
                taskClassName !==
                selectedTaskClassName
            ) {
                continue;
            }

            let element =
            document.createElement(
                "div"
            );

            CB_Admin_ScheduledTasks_shared_containerElement.append(
                element
            );

            element.style.padding =
            "10px";

            element.append(
                CB_Admin_ScheduledTasks_createLine1Element(
                    scheduledTaskObject
                )
            );

            element.append(
                CB_Admin_ScheduledTasks_createLine2Element(
                    scheduledTaskObject
                )
            );

            element.append(
                CB_Admin_ScheduledTasks_createTimeElement(
                    scheduledTaskObject
                )
            );
        }
        // for - scheduled task
    }
    // CB_Admin_ScheduledTasks_render()

}
)();
