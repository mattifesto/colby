"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTasks2Admin */
/* global
    CBErrorHandler,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIMessagePart,
    CBUISection,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,
*/

var CBTasks2Admin = {

    sectionElement: undefined,

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

        Colby.CBTasks2_delay = 0;

        fetchStatus();

        return;

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
                    CBErrorHandler.displayAndReport(error);
                }
            );
        }
    },



    /**
     * @param object status
     *
     * @return undefined
     */
    updateStatus: function (status) {
        let sectionElement = CBTasks2Admin.sectionElement;
        sectionElement.textContent = "";

        sectionElement.appendChild(create("Scheduled Tasks", status.scheduled));
        sectionElement.appendChild(create("Ready Tasks", status.ready));
        sectionElement.appendChild(create("Running Tasks", status.running));
        sectionElement.appendChild(create("Complete Tasks", status.complete));
        sectionElement.appendChild(create("Failed Tasks", status.failed));
        sectionElement.appendChild(create("CBTasks2_delay", Colby.CBTasks2_delay));
        sectionElement.appendChild(create("Tasks Requested", Colby.CBTasks2_countOfTasksRequested));
        sectionElement.appendChild(create("Tasks Run", Colby.CBTasks2_countOfTasksRun));

        /* closure */
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
    },
};

/**
 * By default, this page is an observer of task status, not a page that runs
 * tasks.
 */
Colby.tasks.stop();

Colby.afterDOMContentLoaded(CBTasks2Admin.init);
