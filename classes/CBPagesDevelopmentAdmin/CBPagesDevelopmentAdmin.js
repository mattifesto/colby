"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPagesDevelopmentAdmin */
/* global
    CBAjax,
    CBErrorHandler,
    CBModel,
    CBUI,
    CBUIPanel,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,

    CBPagesDevelopmentAdmin_pages,
*/

(function () {

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();

            sectionItem.callback = function () {
                CBAjax.call(
                    "CBPageVerificationTask",
                    "startForAllPages"
                ).then(
                    function () {
                        CBUIPanel.displayText(
                            "Verification as been started for all pages."
                        );
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayError(error);
                        CBErrorHandler.report(error);
                    }
                );
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

        CBPagesDevelopmentAdmin_pages.forEach(function (page) {
            let category = pageToCategory(page);

            if (pagesByCategory[category] === undefined) {
                pagesByCategory[category] = [];
            }

            pagesByCategory[category].push(page);
        });

        let nonstandardPages = [];

        {
            let sectionElement = CBUI.createSection();

            Object.keys(pagesByCategory).forEach(
                function (key) {
                    let pages = pagesByCategory[key];
                    let first = pages[0];

                    let title = (
                        (
                            first.published === null ?
                            "Unpublished" :
                            "Published"
                        ) +
                        ` ${first.className} (${first.classNameForKind})`
                    );

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
                }
            );

            mainElement.appendChild(
                CBUI.createSectionHeader(
                    {
                        text: "Page Counts",
                    }
                )
            );

            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }

        /**
         * If there is no nonstandard test page on your website, you can run the
         * CBTestPageTests "toggleNonstandardTestPage" test to add one.
         */

        if (nonstandardPages.length > 0) {
            let sectionElement = CBUI.createSection();

            nonstandardPages.forEach(
                function (page) {
                    let elements = CBUI.createElementTree(
                        [
                            "CBUI_sectionItem",
                            "a",
                        ],
                        "CBUI_container_topAndBottom CBUI_flexGrow",
                        "title CBUI_ellipsis"
                    );

                    let sectionItemElement = elements[0];

                    sectionElement.appendChild(
                        sectionItemElement
                    );

                    sectionItemElement.href = (
                        `/admin/?c=CBModelInspector&ID=${page.ID}`
                    );


                    /* title */

                    let titleElement = elements[2];

                    if (page.className === null) {
                        /**
                         * @NOTE 2020_04_12
                         *
                         *      I'm not sure how this scenario happens.
                         */

                        titleElement.textContent = "No Model";
                    } else {
                        let pageClassName = CBModel.valueToString(
                            page,
                            "className"
                        );

                        let pageTitle = CBModel.valueToString(
                            page,
                            "title"
                        ).trim();

                        if (pageTitle === "") {
                            pageTitle = "<empty title>";
                        }

                        titleElement.textContent = (
                            `${pageClassName} | ${pageTitle}`
                        );
                    }


                    /* navigation arrow */

                    sectionItemElement.appendChild(
                        CBUI.createElement(
                            "CBUI_navigationArrow"
                        )
                    );
                }
            );

            mainElement.appendChild(
                CBUI.createSectionHeader(
                    {
                        text: "Nonstandard Pages",
                    }
                )
            );

            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @param object page
     *
     * @return string
     */
    function pageToCategory(
        page
    ) {
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
    }
    /* pageToCategory() */

})();
