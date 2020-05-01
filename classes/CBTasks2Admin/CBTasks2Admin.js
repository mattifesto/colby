"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTasks2Admin */
/* global
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIMessagePart,
    CBUIPanel,
    CBUISection,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,

    CBTasks2Admin_failedTasks,
*/



var CBTasks2Admin = {

    sectionElement: undefined,



    /* -- functions -- -- -- -- -- */



    /**
     * @return Element
     */
    init: function () {
        let maintenanceMessagePart;
        var mainElement = document.getElementsByTagName("main")[0];

        appendHeader(mainElement);
        appendControlSection();

        {
            let result = appendStatusSection(mainElement);
            CBTasks2Admin.sectionElement = result.sectionElement;
        }

        {
            let result = appendMaintenanceSection(mainElement);
            maintenanceMessagePart = result.messagePart;
        }

        mainElement.appendChild(
            createFailedTasksElement()
        );

        Colby.CBTasks2_delay = 0;

        fetchStatus();

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function appendControlSection() {
            let section = CBUISection.create();
            let sectionItem = CBUISectionItem4.create();
            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Process Tasks";
            let booleanSwitchPart = CBUIBooleanSwitchPart.create();
            booleanSwitchPart.changed = function () {
                if (booleanSwitchPart.value) {
                    Colby.tasks.start();
                } else {
                    Colby.tasks.stop();
                }
            };

            sectionItem.appendPart(stringsPart);
            sectionItem.appendPart(booleanSwitchPart);
            section.appendItem(sectionItem);
            mainElement.appendChild(section.element);
            mainElement.appendChild(CBUI.createHalfSpace());
        }
        /* appendControlSection() */



        /**
         * CBTasks2Admin.init() closure
         *
         * @param Element parentElement
         *
         * @return undefined
         */
        function appendHeader(parentElement) {
            parentElement.appendChild(
                CBUI.createHeader({
                    centerElement: CBUI.createHeaderTitle({
                        text: "Tasks"
                    }),
                })
            );

            parentElement.appendChild(CBUI.createHalfSpace());
        }
        /* appendHeader() */



        /**
         * CBTasks2Admin.init() closure
         *
         * @param Element parentElement
         *
         * @return object
         *
         *      {
         *          stringsPart: CBUIMessagePart
         *      }
         *
         *      The message part is returned so that it can be updated with the
         *      current maintenance status.
         */
        function appendMaintenanceSection(parentElement) {
            mainElement.appendChild(
                CBUI.createSectionHeader({
                    text: "Maintenance"
                })
            );

            let section = CBUISection.create();
            let sectionItem = CBUISectionItem4.create();
            let messagePart = CBUIMessagePart.create();

            sectionItem.appendPart(messagePart);
            section.appendItem(sectionItem);
            parentElement.appendChild(section.element);
            parentElement.appendChild(CBUI.createHalfSpace());

            return {
                messagePart: messagePart,
            };
        }
        /* appendMaintenanceSection() */



        /**
         * CBTasks2Admin.init() closure
         *
         * @param Element parentElement
         *
         * @return object
         *
         *      {
         *          sectionElement: Element
         *      }
         *
         *      The section element is returned so that it can be updated with
         *      the current task status.
         */
        function appendStatusSection(parentElement) {
            let section = CBUISection.create();

            parentElement.appendChild(section.element);
            parentElement.appendChild(CBUI.createHalfSpace());

            return {
                sectionElement: section.element,
            };
        }
        /* appendStatusSection() */



        /**
         * @return Element
         */
        function createFailedTaskSectionItemElement(
            failedTask
        ) {
            let taskIsCurrentlyRunning = false;

            let taskCBID = CBModel.valueAsID(
                failedTask,
                "CBID"
            );

            let taskClassName = CBModel.valueToString(
                failedTask,
                "className"
            );


            /* element */

            let element = CBUI.createElement(
                "CBUI_container_topAndBottom"
            );

            element.addEventListener(
                "click",
                function () {
                    if (taskIsCurrentlyRunning) {
                        return;
                    }

                    taskIsCurrentlyRunning = true;

                    Colby.callAjaxFunction(
                        "CBTasks2",
                        "runSpecificTask",
                        {
                            className: taskClassName,
                            CBID: taskCBID,
                        }
                    ).catch(
                        function (error) {
                            CBUIPanel.displayAndReportError(error);
                        }
                    ).finally(
                        function () {
                            taskIsCurrentlyRunning = false;
                        }
                    );
                }
            );


            /* title */

            let titleElement = CBUI.createElement();

            element.appendChild(titleElement);

            titleElement.textContent = taskClassName;


            /* description */

            let descriptionElement = CBUI.createElement(
                "CBUI_textColor2 CBUI_textSize_small"
            );

            element.appendChild(descriptionElement);

            descriptionElement.textContent = taskCBID;


            /* done */

            return element;
        }
        /* createFailedTaskSectionItemElement() */



        /**
         * @return Element
         */
        function createFailedTasksElement() {
            let element = CBUI.createElement(
                "CBTasks2Admin_failedTasks"
            );

            if (CBTasks2Admin_failedTasks.length === 0) {
                return element;
            }


            /* title */

            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            element.appendChild(titleElement);

            titleElement.textContent = "Failed Tasks";


            /* section container */

            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(sectionContainerElement);


            /* section */

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);


            /* failed tasks */

            CBTasks2Admin_failedTasks.forEach(
                function (failedTask) {
                    sectionElement.appendChild(
                        createFailedTaskSectionItemElement(
                            failedTask
                        )
                    );
                }
            );


            /* done */

            return element;
        }
        /* createFailedTasksSectionElement() */



        /**
         * CBTasks2Admin.init() closure
         *
         * @return undefined
         */
        function fetchStatus() {
            Colby.callAjaxFunction(
                "CBTasks2",
                "fetchStatus"
            ).then(
                function (value) {
                    maintenanceMessagePart.message = value.maintenanceIsLocked ?
                        '(The site is locked for maintenance (b)).' :
                        'The site is operating normally.';

                    CBTasks2Admin.updateStatus(value);

                    setTimeout(fetchStatus, 500);
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );
        }
        /* fetchStatus() */

    },
    /* init() */


    /**
     * @param object status
     *
     * @return undefined
     */
    updateStatus: function (status) {
        let sectionElement = CBTasks2Admin.sectionElement;
        sectionElement.textContent = "";

        sectionElement.appendChild(create(
            "Scheduled Tasks",
            status.scheduled
        ));

        sectionElement.appendChild(create(
            "Ready Tasks",
            status.ready
        ));

        sectionElement.appendChild(create(
            "Running Tasks",
            status.running
        ));
        sectionElement.appendChild(create(
            "Complete Tasks",
            status.complete
        ));

        sectionElement.appendChild(create(
            "Failed Tasks",
            status.failed
        ));

        sectionElement.appendChild(create(
            "CBTasks2_delay",
            Colby.CBTasks2_delay
        ));

        sectionElement.appendChild(create(
            "Tasks Requested",
            Colby.CBTasks2_countOfTasksRequested
        ));

        sectionElement.appendChild(create(
            "Tasks Run",
            Colby.CBTasks2_countOfTasksRun
        ));



        /* -- closures -- -- -- -- -- */



        /**
         *
         */
        function create(text, value) {
            let sectionItem = CBUISectionItem4.create();
            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = text;
            stringsPart.string2 = value;

            stringsPart.element.classList.add("keyvalue");
            stringsPart.element.classList.add("sidebyside");

            sectionItem.appendPart(stringsPart);

            return sectionItem.element;
        }
        /* create() */
    },
    /* updateStatus() */

};

/**
 * By default, this page is an observer of task status, not a page that runs
 * tasks.
 */
Colby.tasks.stop();

Colby.afterDOMContentLoaded(CBTasks2Admin.init);
