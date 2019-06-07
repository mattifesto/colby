"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpecEditor_Tests */
/* global
    CBTest,
    CBUISpecEditor,
*/

var CBUISpecEditor_Tests = {

    /**
     * @return object
     */
    CBTest_wellKnownModels: function () {
        let specs = [
            {
                className: "CBArtworkView",
                image: 5,
            },
            {
                className: "CBBackgroundView",
                image: 5,
            },
            {
                className: "CBContainerView",
                smallImage: 5,
            },
            {
                className: "CBContainerView2",
                image: 5,
            },
            {
                className: "CBIconLinkView",
                image: 5,
            },
            {
                className: "CBLinkView1",
                image: 5,
            },
            {
                className: "CBMenuView",
            },
            /* uses installed admin menu */
            {
                className: "CBMenuView",
                menuID: "3924c0a0581171f86f0708bfa799a3d8c34bd390",
                selectedItemName: "help",
            },
            {
                className: "CBPageListView2",
            },
            {
                className: "CBSitePreferences",
                imageForIcon: 5,
            },
            {
                className: "CBViewPage",
                image: 5,
            },
        ];

        for (let index = 0; index < specs.length; index += 1) {
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
                        This spec did not product an editor element.
                    `
                );
            }
        }
        /* for */

        return {
            succeeded: true,
        };
    },
    /* CBTest_wellKnownModels() */
};
/* CBUISpecEditor_Tests */
