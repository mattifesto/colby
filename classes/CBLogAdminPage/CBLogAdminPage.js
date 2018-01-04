"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLogAdminPage */
/* globals
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUISelector,
    Colby */

var CBLogAdminPage = {

    /**
     * @return Element
     */
    create: function () {
        let navigator = CBUINavigationView.create();
        var args = {
            lowestSeverity: 6,
            mostRecentDescending: true,
        };

        var entriesElement = document.createElement("div");
        entriesElement.className = "entries";

        {
            let containerElement = document.createElement("div");

            containerElement.appendChild(CBUI.createHalfSpace());

            let sectionElement = CBUI.createSection();
            let itemElement = CBUI.createSectionItem();
            let options = [
                { title: "0: Emergency", value: 0 },
                { title: "1: Alert", value: 1 },
                { title: "2: Critical", value: 2 },
                { title: "3: Error", value: 3 },
                { title: "4: Warning", value: 4 },
                { title: "5: Notice", value: 5 },
                { title: "6: Informational", value: 6 },
                { title: "7: Debug", value: 7 },
            ];
            let selector = CBUISelector.create({
                labelText: 'Lowest Severity',
                navigateToItemCallback: navigator.navigateToItemCallback,
                options: options,
                propertyName: "lowestSeverity",
                spec: args,
                specChangedCallback: handleArgsChanged,
            });

            itemElement.appendChild(selector.element);
            sectionElement.appendChild(itemElement);
            containerElement.appendChild(sectionElement);
            containerElement.appendChild(CBUI.createHalfSpace());

            containerElement.appendChild(entriesElement);

            navigator.navigateToItemCallback({
                element: containerElement,
            });
        }

        handleArgsChanged();

        return navigator.element;

        function handleArgsChanged() {
            Colby.callAjaxFunction("CBLog", "fetchEntries", args)
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);
        }

        function onFulfilled(entries) {
            var count = 0;

            entriesElement.textContent = "";

            for (let entry of entries) {
                var message = entry.message;

                entriesElement.appendChild(CBUIExpander.create({
                    message: message,
                    severity: entry.severity,
                    timestamp: entry.timestamp,
                }).element);

                count += 1;

                if (count >= 100) {
                    break;
                }
            }

            Colby.updateTimes();
        }
    },
};

Colby.afterDOMContentLoaded(function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBLogAdminPage.create());
});
