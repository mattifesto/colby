"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpecEditor_Tests */
/* global
    CBTest,
    CBUISpecEditor,

    CBUISpecEditor_Tests_editableModelClassNames,
*/

var CBUISpecEditor_Tests = {

    /**
     * @return object
     */
    CBTest_allModelEditors: function () {
        let specs = [];

        CBUISpecEditor_Tests_editableModelClassNames.forEach(
            function (editableModelClassName) {
                specs.push(
                    {
                        ID: "0000111122223333444455556666777788889999",
                        className: editableModelClassName,
                    }
                );
            }
        );

        for (
            let index = 0;
            index < specs.length;
            index += 1
        ) {
            let spec = specs[index];
            let editor = CBUISpecEditor.create(
                {
                    spec,
                    specChangedCallback: function () {},
                    useStrict: true,
                }
            );

            if (editor.element === undefined) {
                return CBTest.valueIssueFailure(
                    `spec at index ${index}`,
                    spec,
                    `
                        This spec did not produce an editor element.
                    `
                );
            }
        }
        /* for */

        return {
            succeeded: true,
        };
    },
    /* CBTest_allModelEditors() */

};
/* CBUISpecEditor_Tests */
