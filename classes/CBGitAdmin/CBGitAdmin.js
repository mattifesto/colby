"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBGitAdmin */
/* global
    CBGitAdmin_submodules,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUISelector,
    Colby */

var CBGitAdmin = {

    init: function () {
        let main = document.getElementsByTagName("main")[0];
        let navigator = CBUINavigationView.create();

        main.appendChild(navigator.element);

        let element = document.createElement("div");

        element.appendChild(CBUI.createHalfSpace());

        let monthSelector = CBUISelector.create();
        let yearSelector = CBUISelector.create();
        let submoduleSelector = CBUISelector.create();

        {
            let sectionElement = CBUI.createSection();

            {
                monthSelector.options = [
                    { title: "January", value: 0 },
                    { title: "February", value: 1 },
                    { title: "March", value: 2 },
                    { title: "April", value: 3 },
                    { title: "May", value: 4 },
                    { title: "June", value: 5 },
                    { title: "July", value: 6 },
                    { title: "August", value: 7 },
                    { title: "September", value: 8 },
                    { title: "October", value: 9 },
                    { title: "November", value: 10 },
                    { title: "December", value: 11 },
                ];
                monthSelector.value = new Date().getMonth();
                monthSelector.onchange = fetch;
                sectionElement.appendChild(monthSelector.element);
            }

            {
                let options = [];
                let year = new Date().getFullYear();

                for (let i = 0; i < 20; i++) {
                    options.push({
                        title: year - i,
                        value: year - i,
                    });
                }

                yearSelector.options = options;
                yearSelector.value = year;
                yearSelector.onchange = fetch;

                sectionElement.appendChild(yearSelector.element);
            }

            {
                let options = [
                    { title: "website" }
                ];

                CBGitAdmin_submodules.forEach(function (name) {
                    options.push({ title: name, value: name });
                });

                submoduleSelector.options = options;
                submoduleSelector.onchange = fetch;

                sectionElement.appendChild(submoduleSelector.element);
            }

            element.appendChild(sectionElement);
            element.appendChild(CBUI.createHalfSpace());
        }

        let expander = CBUIExpander.create();

        element.appendChild(expander.element);
        element.appendChild(CBUI.createHalfSpace());

        navigator.navigate({
            element: element,
            title: "Git",
        });

        fetch();

        /* closure */
        function fetch() {
            let args = {
                month: monthSelector.value + 1,
                year: yearSelector.value,
                submodule: submoduleSelector.value,
            };

            Colby.callAjaxFunction("CBGitAdmin", "fetch", args)
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);

            /* closure */
            function onFulfilled (value) {
                expander.message = value;
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBGitAdmin.init);
