"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBHideByUserGroupViewEditor */
/* global
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUISelector,
    CBUISpecArrayEditor,

    CBHideByUserGroupViewEditor_addableClassNames,
    CBHideByUserGroupViewEditor_userGroupClassNames,
*/



var CBHideByUserGroupViewEditor = {

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;

        var element = document.createElement("div");
        element.className = "CBHideByUserGroupViewEditor";

        var groupOptions = [
            {
                title: "None",
                description: (
                    "Subviews are always hidden."
                ),
                value: undefined
            },
        ];

        CBHideByUserGroupViewEditor_userGroupClassNames.forEach(
            function (groupName) {
                groupOptions.push(
                    {
                        title: groupName,
                        value: groupName,
                    }
                );
            }
        );

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUISelector.create(
                {
                    labelText: "User Group",
                    propertyName: "userGroupClassName",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                    options: groupOptions,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Hide from members of group",
                    propertyName: "hideFromMembers",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Hide from nonmembers of group",
                    propertyName: "hideFromNonmembers",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(
            CBUI.createHalfSpace()
        );

        /* subviews */
        {
            if (args.spec.subviews === undefined) {
                args.spec.subviews = [];
            }

            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: (
                        CBHideByUserGroupViewEditor_addableClassNames
                    ),
                    specs: args.spec.subviews,
                    specsChangedCallback: args.specChangedCallback,
                }
            );

            editor.title = "Views";

            element.appendChild(editor.element);

            element.appendChild(
                CBUI.createHalfSpace()
            );
        }

        return element;
    },
    /* createEditor() */



    /**
     * @param object spec
     *
     *      {
     *          userGroupClassName: string
     *          hideFromMembers: bool
     *          hideFromNonmembers: bool
     *      }
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let userGroupClassName = CBModel.valueAsName(
            spec,
            "userGroupClassName"
        );


        /* no user group class name is selected */

        if (userGroupClassName === undefined) {
            return "Subviews are always hidden.";
        }


        /* hidden from all or shown to all */

        if (
            spec.hideFromMembers &&
            spec.hideFromNonmembers
        ) {
            return "Subviews are hidden from everyone";
        } else if (
            !spec.hideFromMembers &&
            !spec.hideFromNonmembers
        ) {
            return "Subviews are shown to everyone";
        }


        if (spec.hideFromMembers) {
            return (
                "Subviews are hidden from members of " + userGroupClassName
            );
        }


        if (spec.hideFromNonmembers) {
            return (
                "Subviews are hidden from nonmembers of " + userGroupClassName
            );
        }


        /* unreachable */

        return undefined;
    },
    /* CBUISpec_toDescription() */

};
