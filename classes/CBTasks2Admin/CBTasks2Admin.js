"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTasks2Admin */
/* global
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby */

var CBTasks2Admin = {

    sectionElement: undefined,

    /**
     * @return Element
     */
    init: function () {
        Colby.CBTasks2RunAlways = true;

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
     * @return Element
     */
    updateStatus: function (status) {
        let sectionElement = CBTasks2Admin.sectionElement;
        sectionElement.textContent = "";

        sectionElement.appendChild(create("Scheduled Tasks", status.scheduled));
        sectionElement.appendChild(create("Ready Tasks", status.ready));
        sectionElement.appendChild(create("Running Tasks", status.running));
        sectionElement.appendChild(create("Complete Tasks", status.complete));
        sectionElement.appendChild(create("Failed Tasks", status.failed));
        sectionElement.appendChild(create("CBTasks2Delay", Colby.CBTasks2Delay));

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

        function fetchStatus() {
            Colby.callAjaxFunction("CBTasks2", "fetchStatus")
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);
        }

        function onFulfilled(value) {
            CBTasks2Admin.updateStatus(value);

            if (value.ready > 0) {
                Colby.CBTasks2Delay = 1; // 1 millisecond
            } else {
                Colby.CBTasks2Delay = 2000; // 2 seconds
            }

            setTimeout(fetchStatus, 1000);
        }
    },
};

Colby.afterDOMContentLoaded(CBTasks2Admin.init);
