/* global
    CBAjax,
    CBModel,
    Colby,
*/


(function ()
{
    let CB_Admin_ScheduledTasks_shared_containerElement;

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
     * @return undefined
     */
    async function
    CB_Admin_ScheduledTasks_refresh(
    ) // -> undefined
    {
        let result =
        await
        CBAjax.call2(
            "CB_Ajax_FetchScheduledTasks"
        );

        CB_Admin_ScheduledTasks_shared_containerElement.textContent =
        "";

        let scheduledTaskObjects =
        CBModel.valueToArray(
            result,
            "CB_Ajax_FetchScheduledTasks_scheduledTasks"
        );

        let scheduledTasksCount =
        scheduledTaskObjects.length;

        for (
            let scheduledTaskIndex = 0;
            scheduledTaskIndex < scheduledTasksCount;
            scheduledTaskIndex++
        ) {
            let scheduledTaskObject =
            scheduledTaskObjects[
                scheduledTaskIndex
            ];

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
    // CB_Admin_ScheduledTasks_refresh()

}
)();
