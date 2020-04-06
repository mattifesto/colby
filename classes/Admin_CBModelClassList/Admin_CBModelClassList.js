"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBUI,
    CBUINavigationArrowPart,
    CBUISectionItem4,
    CBUITitleAndDescriptionPart,
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
                let sectionItem = CBUISectionItem4.create();

                let titleAndDescriptionPart =
                CBUITitleAndDescriptionPart.create();

                titleAndDescriptionPart.title = className;

                sectionItem.callback = function () {
                    window.location = (
                        "/admin/?c=Admin_CBModelList&modelClassName=" +
                        className
                    );
                };

                sectionItem.appendPart(titleAndDescriptionPart);

                sectionItem.appendPart(
                    CBUINavigationArrowPart.create()
                );

                sectionElement.appendChild(sectionItem.element);
            }
        );

        mainElement.appendChild(sectionElement);

        mainElement.appendChild(
            CBUI.createHalfSpace()
        );
    }
    /* renderClassNameList() */

})();
