"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTasks2Admin */
/* global
    CBUI,
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
        Colby.CBTasks2_delay = 0;

        var mainElement = document.getElementsByTagName("main")[0];
        CBTasks2Admin.sectionElement = CBUI.createSection();

        mainElement.appendChild(CBUI.createHeader({
            centerElement: CBUI.createHeaderTitle({text: "Tasks"}),
        }));
        mainElement.appendChild(CBUI.createHalfSpace());
        mainElement.appendChild(CBTasks2Admin.sectionElement);
        mainElement.appendChild(CBUI.createHalfSpace());

        CBTasks2Admin.startFetchingStatus();
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

    /**
     * @return undefined
     */
    startFetchingStatus: function (element) {
        fetchStatus();

        /* closure */
        function fetchStatus() {
            Colby.callAjaxFunction("CBTasks2", "fetchStatus")
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);
        }

        /* closure */
        function onFulfilled(value) {
            CBTasks2Admin.updateStatus(value);

            setTimeout(fetchStatus, 500);
        }
    },
};

Colby.afterDOMContentLoaded(CBTasks2Admin.init);
