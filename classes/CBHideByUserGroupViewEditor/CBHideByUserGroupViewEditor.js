"use strict";
/* jshint strict: global */
/* exported CBHideByUserGroupViewEditor */
/* global
    CBArrayEditor,
    CBHideByUserGroupViewEditorAddableViews,
    CBHideByUserGroupViewEditorGroupNames,
    CBUI,
    CBUIBooleanEditor,
    CBUISelector */

var CBHideByUserGroupViewEditor = {

    /**
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBHideByUserGroupViewEditor";
        var groupOptions = [
            {title: "All Visitors", description: "Every visitor, logged in or out, is a member of this group.", value: undefined},
        ];

        CBHideByUserGroupViewEditorGroupNames.forEach(function (groupName) {
            groupOptions.push({
                title: groupName,
                value: groupName,
            });
        });

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText: "User Group",
            navigateToItemCallback: args.navigateToItemCallback,
            propertyName: "groupName",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
            options: groupOptions,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText: "Hide from members of group",
            propertyName: "hideFromMembers",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText: "Hide from nonmembers of group",
            propertyName: "hideFromNonmembers",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        /* subviews */
        element.appendChild(CBUI.createSectionHeader({ text : "Subviews" }));

        if (args.spec.subviews === undefined) { args.spec.subviews = []; }

        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.subviews,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBHideByUserGroupViewEditorAddableViews,
            navigateToItemCallback : args.navigateToItemCallback,
        }));

        element.appendChild(CBUI.createHalfSpace());

        return element;
    },

    /**
     * @param string? spec.groupName
     * @param bool? spec.hideFromMembers
     * @param bool? spec.hideFromNonmembers
     *
     * @return string|undefined
     */
    specToDescription: function (spec) {
        if (spec.hideFromMembers && spec.hideFromNonmembers) {
            return "Hidden from everyone";
        } else if (!spec.hideFromMembers && !spec.hideFromNonmembers) {
            return "Shown to everyone";
        }

        if (!spec.groupName) {
            if (spec.hideFromMembers) {
                return "Hidden from all visitors";
            }

            if (spec.hideFromNonmembers) {
                return "Shown to all visitors";
            }
        }

        if (spec.hideFromMembers) {
            return "Hidden from members of " + spec.groupName;
        }

        if (spec.hideFromNonmembers) {
            return "Hidden from nonmembers of " + spec.groupName;
        }

        // Unreachable
        return undefined;
    },
};
