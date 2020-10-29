"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBAjax,
    CBUI,
    CBUINavigationView,
    CBUIPanel,
    CBUISelector,
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


        CBAjax.call(
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
                CBUIPanel.displayAndReportError(error);
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
                    CBAjax.call(
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
                            CBUIPanel.displayAndReportError(error);
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
                        let elements = CBUI.createElementTree(
                            [
                                "CBUI_sectionItem",
                                "a",
                            ],
                            "CBUI_container_topAndBottom CBUI_flexGrow",
                            "CBDataStoresAdminPage_title"
                        );

                        dataStoresSectionElement.appendChild(
                            elements[0]
                        );

                        let sectionItemElement = elements[0];

                        sectionItemElement.href = (
                            "/admin/?c=CBModelInspector&ID=" +
                            value.ID
                        );

                        let textContainerElement = elements[1];
                        let titleElement = elements[2];

                        titleElement.textContent = value.ID;

                        let descriptionElement = CBUI.createElement(
                            "CBUI_textSize_small CBUI_textColor2"
                        );

                        textContainerElement.appendChild(
                            descriptionElement
                        );

                        descriptionElement.textContent = (
                            value.className || "No model"
                        );

                        sectionItemElement.appendChild(
                            CBUI.createElement(
                                "CBUI_navigationArrow"
                            )
                        );
                    }
                }
            );
        }
        /* update() */

    }
    /* createRootPaneElement() */

})();
