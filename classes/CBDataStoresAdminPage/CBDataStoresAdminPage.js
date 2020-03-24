"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    CBUINavigationArrowPart,
    CBUINavigationView,
    CBUIPanel,
    CBUISectionItem4,
    CBUISelector,
    CBUITitleAndDescriptionPart,
    Colby,
*/



(function () {

    Colby.afterDOMContentLoaded(afterDOMContentLoaded);



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let mainElement;

        {
            let elements = document.getElementsByClassName(
                "CBDataStoresAdminPage"
            );

            if (elements.length < 1) {
                return;
            }

            mainElement = elements.item(0);

            let navigationView = CBUINavigationView.create();

            mainElement.appendChild(
                navigationView.element
            );
        }


        Colby.callAjaxFunction(
            "CBDataStoresAdminPage",
            "fetchData"
        ).then(
            function (value) {
                let rootPaneElement = createRootPaneElement(value);

                CBUINavigationView.navigate(
                    {
                        element: rootPaneElement,
                        title: "Data Stores",
                    }
                );
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function createRootPaneElement(
        data
    ) {
        let rootPaneElement = CBUI.createElement(
            "CBDataStoresAdminPage_rootPaneElement"
        );

        {
            let elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            rootPaneElement.appendChild(
                elements[0]
            );

            let buttonElement = elements[1];
            buttonElement.textContent = "Restart Data Store Finder";

            buttonElement.addEventListener(
                "click",
                function () {
                    Colby.callAjaxFunction(
                        "CBDataStoresFinderTask",
                        "restart"
                    ).then(
                        function () {
                            CBUIPanel.displayText(
                                "The data store finder has been restarted."
                            );
                        }
                    ).catch(
                        function (error) {
                            CBErrorHandler.displayAndReport(error);
                        }
                    );
                }
            );
        }

        var classNames = new Set();

        data.forEach(
            function (value) {
                classNames.add(
                    value.className
                );
            }
        );

        var options = [];

        classNames.forEach(
            function (className) {
                var title = className;

                if (!className) {
                    title = "No model";
                    className = undefined;
                }

                options.push(
                    {
                        title: title,
                        value: className
                    }
                );
            }
        );

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            rootPaneElement.appendChild(
                elements[0]
            );


            let sectionElement = elements[1];

            sectionElement.appendChild(
                CBUISelector.create(
                    {
                        labelText: "Class Name",
                        options: options,
                        propertyName: "className",
                        valueChangedCallback: update,
                    }
                ).element
            );
        }

        let dataStoresSectionElement;

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            rootPaneElement.appendChild(
                elements[0]
            );

            dataStoresSectionElement = elements[1];
        }

        update();

        return rootPaneElement;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function update(className) {
            if (!className) {
                className = null;
            }

            dataStoresSectionElement.textContent = "";

            data.forEach(
                function (value) {
                    if (value.className === className) {
                        let sectionItem = CBUISectionItem4.create();

                        sectionItem.callback = function () {
                            window.location = (
                                "/admin/?c=CBModelInspector&ID=" +
                                value.ID
                            );
                        };

                        let titleAndDescriptionPart =
                        CBUITitleAndDescriptionPart.create();

                        titleAndDescriptionPart.title = value.ID;
                        titleAndDescriptionPart.description = (
                            value.className || "No model"
                        );

                        sectionItem.appendPart(
                            titleAndDescriptionPart
                        );

                        sectionItem.appendPart(
                            CBUINavigationArrowPart.create()
                        );

                        dataStoresSectionElement.appendChild(
                            sectionItem.element
                        );
                    }
                }
            );
        }
        /* update() */

    }
    /* createRootPaneElement() */

})();
