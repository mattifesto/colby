"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBDatabaseAdmin */
/* global
    CBDatabaseAdmin_tableMetadataList,
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,
*/

var CBDatabaseAdmin = {

    init: function () {
        CBDatabaseAdmin_tableMetadataList.sort(function (a, b) {
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
        });

        let main = document.getElementsByTagName("main")[0];

        main.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();

            CBDatabaseAdmin_tableMetadataList.forEach(function (tableMetadata) {
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();

                stringsPart.element.classList.add("keyvalue");
                stringsPart.element.classList.add("sidebyside");

                stringsPart.string1 = tableMetadata.tableName;
                stringsPart.string2 = `${tableMetadata.tableSizeInMB} MB`;

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            });

            main.appendChild(sectionElement);
            main.appendChild(CBUI.createHalfSpace());
        }
    },
};

Colby.afterDOMContentLoaded(CBDatabaseAdmin.init);
