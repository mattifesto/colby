"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPagesDevelopmentAdminPage */
/* global
    CBPagesDevelopmentAdminPage_pages,
    CBUI,
    CBUINavigationArrowPart,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby */

var CBPagesDevelopmentAdminPage = {

    /**
     * @return undefined
     */
    init: function () {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                Colby.callAjaxFunction("CBPageVerificationTask", "startForAllPages")
                    .then(function () { Colby.alert("Verification as been started for all pages."); })
                    .catch(Colby.displayAndReportError);
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Start Verification for All Pages";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }

        let pagesByCategory = {};

        CBPagesDevelopmentAdminPage_pages.forEach(function (page) {
            let category = CBPagesDevelopmentAdminPage.pageToCategory(page);

            if (pagesByCategory[category] === undefined) {
                pagesByCategory[category] = [];
            }

            pagesByCategory[category].push(page);
        });

        let nonstandardPages = [];

        {
            let sectionElement = CBUI.createSection();

            Object.keys(pagesByCategory).forEach(function (key) {
                let pages = pagesByCategory[key];
                let first = pages[0];
                let title = (first.published === null ? "Unpublished" : "Published") +
                    ` ${first.className} (${first.classNameForKind})`;
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = title;
                stringsPart.string2 = pages.length;

                stringsPart.element.classList.add("keyvalue");
                stringsPart.element.classList.add("sidebyside");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);

                if (first.className !== "CBViewPage") {
                    nonstandardPages = nonstandardPages.concat(pages);
                }
            });

            mainElement.appendChild(CBUI.createSectionHeader({
                text: "Page Counts",
            }));
            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }

        if (nonstandardPages.length > 0) {
            let sectionElement = CBUI.createSection();

            nonstandardPages.forEach(function (page) {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    window.location = `/admin/?c=CBModelInspector&ID=${page.ID}`;
                };

                let stringsPart = CBUIStringsPart.create();

                if (page.className === null) {
                    stringsPart.string1 = "No Model";
                } else {
                    stringsPart.string1 = page.className +
                        " " +
                        (page.title === null ? "(no title)" : `(${page.title})`);
                }

                sectionItem.appendPart(stringsPart);
                sectionItem.appendPart(CBUINavigationArrowPart.create());
                sectionElement.appendChild(sectionItem.element);
            });

            mainElement.appendChild(CBUI.createSectionHeader({
                text: "Nonstandard Pages",
            }));
            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }
    },

    pageToCategory: function (page) {
        let name = "";

        if (page.published === null) {
            name += "unpublished";
        } else {
            name += "published";
        }

        if (page.className === null) {
            name += "_none";
        } else {
            name += "_" + page.className;
        }

        if (page.classNameForKind === null) {
            name += "_none";
        } else {
            name += "_" + page.classNameForKind;
        }

        return name;
    },
};

Colby.afterDOMContentLoaded(CBPagesDevelopmentAdminPage.init);
