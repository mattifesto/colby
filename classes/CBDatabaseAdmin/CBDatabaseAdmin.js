"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBDatabaseAdmin */
/* global
    CBUI,
    Colby,

    CBDatabaseAdmin_tableMetadataList,
*/

(function () {

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        CBDatabaseAdmin_tableMetadataList.sort(
            function (a, b) {
                let aval = Number(a.tableSizeInMB);
                let bval = Number(b.tableSizeInMB);

                /* sort from largest to smallest */
                if (aval < bval) {
                    return 1;
                } else if (aval > bval) {
                    return -1;
                } else {
                    return 0;
                }
            }
        );

        let mainElement = document.getElementsByTagName("main")[0];
        let sectionElement;

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            mainElement.appendChild(
                elements[0]
            );

            sectionElement = elements[1];
        }

        CBDatabaseAdmin_tableMetadataList.forEach(
            function (tableMetadata) {
                let elements = CBUI.createElementTree(
                    "CBUI_container_leftAndRight",
                    "CBDatabaseAdmin_tableName CBUI_ellipsis"
                );

                let textContainerElement = elements[0];
                let tableNameElement = elements[1];

                sectionElement.appendChild(
                    textContainerElement
                );

                let tableSizeElement = CBUI.createElement(
                    "CBDatabaseAdmin_tableSize"
                );

                textContainerElement.appendChild(
                    tableSizeElement
                );

                tableNameElement.textContent = tableMetadata.tableName;
                tableSizeElement.textContent = `${tableMetadata.tableSizeInMB} MB`;
            }
        );
    }
    /* afterDOMContentLoaded() */

})();
