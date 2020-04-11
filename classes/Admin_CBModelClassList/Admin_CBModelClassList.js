"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBUI,
    Colby,

    Admin_CBModelClassList_modelClassNames,
*/

(function () {

    let mainElement;

    Colby.afterDOMContentLoaded(
        function () {
            mainElement = document.getElementsByTagName("main")[0];

            if (
                mainElement === undefined ||
                !mainElement.classList.contains("Admin_CBModelClassList")
            ) {
                mainElement = undefined;
                return;
            }

            renderModelClassList();
        }
    );



    /* -- closures -- -- -- -- -- */



    /**
     * @return undefined
     */
    function renderModelClassList() {
        mainElement.appendChild(
            CBUI.createHalfSpace()
        );

        let sectionElement = CBUI.createSection();

        Admin_CBModelClassList_modelClassNames.forEach(
            function (className) {
                let elements = CBUI.createElementTree(
                    [
                        "CBUI_sectionItem",
                        "a"
                    ],
                    "CBUI_container_topAndBottom CBUI_flexGrow",
                    "title CBUI_ellipsis"
                );

                let sectionItemElement = elements[0];

                sectionElement.appendChild(
                    sectionItemElement
                );

                sectionItemElement.href = (
                    "/admin/?c=Admin_CBModelList&modelClassName=" +
                    className
                );

                let titleElement = elements[2];
                titleElement.textContent = className;


                sectionItemElement.appendChild(
                    CBUI.createElement(
                        "CBUI_navigationArrow"
                    )
                );
            }
        );

        mainElement.appendChild(sectionElement);

        mainElement.appendChild(
            CBUI.createHalfSpace()
        );
    }
    /* renderClassNameList() */

})();
